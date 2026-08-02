<?php

namespace App\Http\Controllers\User;

use App\Application\User\DTOs\CreateUserDTO;
use App\Application\User\DTOs\UpdateUserDTO;
use App\Application\User\Services\UserManagementService;
use App\Domain\User\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserAvailabilityRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request, UserManagementService $service): View
    {
        Gate::authorize('viewAny', User::class);
        $search = trim((string) $request->query('search', ''));

        return view('users.index', ['users' => $service->search($search), 'search' => $search]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('users.create', ['roles' => UserRole::cases()]);
    }

    public function store(StoreUserRequest $request, UserManagementService $service): RedirectResponse
    {
        $data = $request->validated();
        $service->create(new CreateUserDTO(
            name: (string) $data['name'],
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            email: (string) $data['email'],
            password: (string) $data['password'],
            role: UserRole::from((string) $data['role']),
        ));

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(int $user, UserManagementService $service): View
    {
        $account = $service->get($user);
        Gate::authorize('update', $account);

        return view('users.edit', ['account' => $account, 'roles' => UserRole::cases()]);
    }

    public function update(UpdateUserRequest $request, int $user, UserManagementService $service): RedirectResponse
    {
        $data = $request->validated();
        $service->update($user, new UpdateUserDTO(
            name: (string) $data['name'],
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            email: (string) $data['email'],
            role: UserRole::from((string) $data['role']),
        ), (int) $request->user()->id);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function availability(
        UpdateUserAvailabilityRequest $request,
        int $user,
        UserManagementService $service,
    ): RedirectResponse {
        $isActive = $request->boolean('is_active');
        $service->setAvailability($user, $isActive, (int) $request->user()->id);

        return back()->with('success', $isActive ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.');
    }

    public function password(
        UpdateUserPasswordRequest $request,
        int $user,
        UserManagementService $service,
    ): RedirectResponse {
        $service->updatePassword($user, (string) $request->validated('password'));

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
