<?php

namespace App\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\User;

final class UserPolicy
{
    public function viewAny(User $actor): bool { return $actor->isAdmin(); }

    public function create(User $actor): bool { return $actor->isAdmin(); }

    public function update(User $actor, User $target): bool { return $actor->isAdmin(); }

    public function setAvailability(User $actor, User $target): bool { return $actor->isAdmin(); }

    public function changePassword(User $actor, User $target): bool { return $actor->isAdmin(); }
}
