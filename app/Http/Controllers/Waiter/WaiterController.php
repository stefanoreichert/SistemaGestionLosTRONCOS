<?php

namespace App\Http\Controllers\Waiter;

use App\Application\Waiter\DTOs\WaiterInputDTO;
use App\Application\Waiter\UseCases\CreateWaiterUseCase;
use App\Application\Waiter\UseCases\GetWaiterUseCase;
use App\Application\Waiter\UseCases\ListWaitersUseCase;
use App\Application\Waiter\UseCases\SetWaiterAvailabilityUseCase;
use App\Application\Waiter\UseCases\UpdateWaiterUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Waiter\StoreWaiterRequest;
use App\Http\Requests\Waiter\UpdateWaiterAvailabilityRequest;
use App\Http\Requests\Waiter\UpdateWaiterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaiterController extends Controller
{
    public function index(Request $request, ListWaitersUseCase $useCase): View
    {
        $search = trim((string) $request->query('search', ''));

        return view('waiters.index', [
            'waiters' => $useCase->execute($search),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('waiters.create');
    }

    public function store(StoreWaiterRequest $request, CreateWaiterUseCase $useCase): RedirectResponse
    {
        $useCase->execute($this->toDto($request->validated()));

        return redirect()->route('waiters.index')->with('status', 'Mozo creado correctamente.');
    }

    public function edit(int $waiter, GetWaiterUseCase $useCase): View
    {
        $entity = $useCase->execute($waiter);
        abort_if($entity === null, 404);

        return view('waiters.edit', ['waiter' => $entity]);
    }

    public function update(
        UpdateWaiterRequest $request,
        int $waiter,
        UpdateWaiterUseCase $useCase,
    ): RedirectResponse {
        $useCase->execute($waiter, $this->toDto($request->validated()));

        return redirect()->route('waiters.index')->with('status', 'Mozo actualizado correctamente.');
    }

    public function availability(
        UpdateWaiterAvailabilityRequest $request,
        int $waiter,
        SetWaiterAvailabilityUseCase $useCase,
    ): RedirectResponse {
        $isActive = (bool) $request->boolean('is_active');
        $useCase->execute($waiter, $isActive);

        return redirect()
            ->route('waiters.index')
            ->with(
                'success',
                $isActive
                    ? 'Mozo activado correctamente.'
                    : 'Mozo desactivado correctamente.',
            );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function toDto(array $data): WaiterInputDTO
    {
        return new WaiterInputDTO(
            name: (string) $data['name'],
            employeeCode: isset($data['employee_code']) ? (string) $data['employee_code'] : null,
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
        );
    }
}
