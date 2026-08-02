<?php

namespace Tests\Unit\Policies;

use App\Domain\Table\Entities\Order;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_perform_every_order_operation(): void
    {
        $admin = User::factory()->create();
        $order = $this->order(10);
        $policy = new OrderPolicy;

        $this->assertTrue($policy->view($admin, $order));
        $this->assertTrue($policy->createWithProduct($admin));
        $this->assertTrue($policy->addProduct($admin, $order));
        $this->assertTrue($policy->modify($admin, $order));
        $this->assertTrue($policy->close($admin, $order));
    }

    public function test_waiter_can_modify_only_owned_orders_and_claim_unassigned_orders(): void
    {
        $waiter = WaiterModel::query()->create(['name' => 'Propietario', 'is_active' => true]);
        $user = User::factory()->waiter((int) $waiter->id)->create();
        $policy = new OrderPolicy;

        $this->assertTrue($policy->addProduct($user, $this->order(null)));
        $this->assertTrue($policy->addProduct($user, $this->order((int) $waiter->id)));
        $this->assertTrue($policy->modify($user, $this->order((int) $waiter->id)));
        $this->assertFalse($policy->modify($user, $this->order(null)));
        $this->assertFalse($policy->addProduct($user, $this->order((int) $waiter->id + 1)));
        $this->assertFalse($policy->close($user, $this->order((int) $waiter->id + 1)));
    }

    public function test_inactive_waiter_and_cashier_cannot_claim_an_unassigned_order(): void
    {
        $waiter = WaiterModel::query()->create(['name' => 'Inactivo', 'is_active' => false]);
        $inactive = User::factory()->waiter((int) $waiter->id)->create();
        $cashier = User::factory()->create(['role' => 'cashier']);
        $policy = new OrderPolicy;

        $this->assertFalse($policy->createWithProduct($inactive));
        $this->assertFalse($policy->addProduct($inactive, $this->order(null)));
        $this->assertFalse($policy->createWithProduct($cashier));
        $this->assertFalse($policy->modify($cashier, $this->order((int) $waiter->id)));
    }

    private function order(?int $waiterId): Order
    {
        return new Order(
            id: 1,
            tableId: 1,
            tableNumber: 1,
            status: 'open',
            subtotalInCents: 0,
            totalInCents: 0,
            paymentMethod: null,
            ticketNumber: null,
            waiterId: $waiterId,
            waiterName: null,
            openedAt: now()->toDateTimeString(),
            closedAt: null,
            items: [],
        );
    }
}
