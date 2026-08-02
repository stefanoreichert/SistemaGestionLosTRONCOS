<?php

namespace App\Application\User\Services;

use App\Application\User\DTOs\CreateUserDTO;
use App\Application\User\DTOs\UpdateUserDTO;
use App\Domain\User\Enums\UserRole;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class UserManagementService
{
    public function search(string $term): LengthAwarePaginator
    {
        return User::query()->with('waiter')
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%')
                        ->orWhere('phone', 'like', '%'.$term.'%');
                });
            })
            ->orderBy('name')->paginate(20)->withQueryString();
    }

    public function get(int $id): User
    {
        return User::query()->with('waiter')->findOrFail($id);
    }

    public function create(CreateUserDTO $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => trim($data->name),
                'phone' => $this->optional($data->phone),
                'email' => mb_strtolower(trim($data->email)),
                'password' => Hash::make($data->password),
                'role' => $data->role->value,
                'is_active' => true,
                'waiter_id' => null,
            ]);

            if ($data->role === UserRole::MOZO) {
                $this->attachOperationalWaiter($user);
            }

            return $user->fresh('waiter');
        });
    }

    public function update(int $id, UpdateUserDTO $data, int $actorId): User
    {
        return DB::transaction(function () use ($id, $data, $actorId): User {
            $user = User::query()->lockForUpdate()->findOrFail($id);

            if ($user->id === $actorId && $user->isAdmin() && $data->role !== UserRole::ADMIN) {
                throw ValidationException::withMessages(['role' => 'No puede quitarse su propio acceso administrativo.']);
            }

            if ($user->isAdmin() && $data->role !== UserRole::ADMIN && $user->is_active) {
                $this->ensureAnotherActiveAdminExists($user->id, 'role');
            }

            $wasWaiter = $user->isWaiter();
            $user->forceFill([
                'name' => trim($data->name),
                'phone' => $this->optional($data->phone),
                'email' => mb_strtolower(trim($data->email)),
                'role' => $data->role->value,
            ]);

            if ($wasWaiter && $data->role !== UserRole::MOZO) {
                $this->deactivateAndDetachWaiter($user);
            }

            $user->save();

            if ($data->role === UserRole::MOZO) {
                $this->attachOperationalWaiter($user);
            }

            return $user->fresh('waiter');
        });
    }

    public function setAvailability(int $id, bool $isActive, int $actorId): User
    {
        return DB::transaction(function () use ($id, $isActive, $actorId): User {
            $user = User::query()->lockForUpdate()->findOrFail($id);

            if (! $isActive && $user->id === $actorId) {
                throw ValidationException::withMessages(['is_active' => 'No puede desactivar su propia cuenta.']);
            }

            if (! $isActive && $user->isAdmin() && $user->is_active) {
                $this->ensureAnotherActiveAdminExists($user->id, 'is_active');
            }

            $user->is_active = $isActive;
            $user->save();

            if ($user->isWaiter()) {
                $this->attachOperationalWaiter($user, $isActive);
            }

            return $user->fresh('waiter');
        });
    }

    public function updatePassword(int $id, string $password): void
    {
        User::query()->findOrFail($id)->forceFill(['password' => Hash::make($password)])->save();
    }

    private function attachOperationalWaiter(User $user, ?bool $isActive = null): void
    {
        $waiter = $user->waiter_id !== null
            ? WaiterModel::query()->lockForUpdate()->find($user->waiter_id)
            : WaiterModel::query()->where('employee_code', $this->employeeCode($user))->lockForUpdate()->first();

        if ($waiter !== null && User::query()->where('waiter_id', $waiter->id)->whereKeyNot($user->id)->exists()) {
            throw ValidationException::withMessages(['role' => 'El perfil operativo de mozo ya pertenece a otra cuenta.']);
        }

        $values = [
            'name' => $user->name,
            'phone' => $user->phone,
            'is_active' => $isActive ?? (bool) $user->is_active,
        ];

        if ($waiter === null) {
            $waiter = WaiterModel::query()->create($values + ['employee_code' => $this->employeeCode($user)]);
        } else {
            $waiter->update($values);
        }

        if ((int) $user->waiter_id !== (int) $waiter->id) {
            $user->waiter_id = $waiter->id;
            $user->save();
        }
    }

    private function deactivateAndDetachWaiter(User $user): void
    {
        if ($user->waiter_id !== null) {
            WaiterModel::query()->whereKey($user->waiter_id)->update(['is_active' => false]);
        }

        $user->waiter_id = null;
    }

    private function ensureAnotherActiveAdminExists(int $excludedUserId, string $field): void
    {
        $ids = User::query()->where('role', UserRole::ADMIN->value)->where('is_active', true)
            ->whereKeyNot($excludedUserId)->lockForUpdate()->pluck('id');

        if ($ids->isEmpty()) {
            throw ValidationException::withMessages([$field => 'Debe existir al menos un administrador activo.']);
        }
    }

    private function employeeCode(User $user): string
    {
        return 'USR-'.(int) $user->id;
    }

    private function optional(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
