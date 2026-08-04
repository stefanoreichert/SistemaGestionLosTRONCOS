<?php

namespace App\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\User;

final class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function update(User $actor, User $target): bool
    {
        // Admins pueden actualizar cualquier usuario
        if ($actor->isAdmin()) {
            return true;
        }

        // Los usuarios solo pueden actualizar su propia información
        return $actor->id === $target->id;
    }

    public function setAvailability(User $actor, User $target): bool
    {
        // Admins pueden cambiar disponibilidad de cualquiera
        if ($actor->isAdmin()) {
            return true;
        }

        // Los usuarios solo pueden cambiar su propia disponibilidad
        return $actor->id === $target->id;
    }

    public function changePassword(User $actor, User $target): bool
    {
        // Admins pueden cambiar contraseña de cualquiera
        if ($actor->isAdmin()) {
            return true;
        }

        // Los usuarios solo pueden cambiar su propia contraseña
        return $actor->id === $target->id;
    }
}
