<?php

namespace App\Policies;

use App\Domain\Table\Entities\Order;
use App\Infrastructure\Persistence\Eloquent\Models\User;

final class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isWaiter();
    }

    public function createWithProduct(User $user): bool
    {
        return $user->isAdmin() || $this->hasActiveWaiter($user);
    }

    public function addProduct(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isWaiter() || $user->waiter_id === null) {
            return false;
        }

        if ($order->waiterId() === null) {
            return $this->hasActiveWaiter($user);
        }

        return $order->waiterId() === (int) $user->waiter_id;
    }

    public function modify(User $user, Order $order): bool
    {
        return $this->ownsOrderOrIsAdmin($user, $order);
    }

    public function close(User $user, Order $order): bool
    {
        return $this->ownsOrderOrIsAdmin($user, $order);
    }

    public function viewKitchen(User $user): bool
    {
        return $user->isAdmin() || $user->isKitchen();
    }

    public function transitionKitchen(User $user): bool
    {
        return $user->isAdmin() || $user->isKitchen();
    }

    private function ownsOrderOrIsAdmin(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isWaiter()
            && $user->waiter_id !== null
            && $order->waiterId() !== null
            && $order->waiterId() === (int) $user->waiter_id;
    }

    private function hasActiveWaiter(User $user): bool
    {
        return $user->isWaiter()
            && $user->waiter_id !== null
            && $user->waiter()->where('is_active', true)->exists();
    }
}
