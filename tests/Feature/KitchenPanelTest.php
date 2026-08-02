<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_kitchen_can_use_panel_but_waiter_and_cashier_cannot(): void
    {
        $admin = User::factory()->create();
        $kitchen = User::factory()->create(['role' => User::ROLE_KITCHEN]);
        $waiter = User::factory()->create(['role' => User::ROLE_MOZO]);
        $cashier = User::factory()->create(['role' => User::ROLE_CAJA]);

        $this->actingAs($admin)->get(route('kitchen.index'))->assertOk();
        $this->actingAs($kitchen)->get(route('kitchen.index'))->assertOk();
        $this->actingAs($waiter)->get(route('kitchen.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('kitchen.index'))->assertForbidden();
    }

    public function test_queue_contains_only_open_non_retired_orders_with_kitchen_items(): void
    {
        $admin = User::factory()->create();
        $visible = $this->order('PENDING', true);
        $this->order(null, false);
        $this->order('RETIRED', true);
        $this->order('READY', true, 'closed');

        $this->actingAs($admin)->getJson(route('kitchen.orders'))
            ->assertOk()
            ->assertJsonCount(1, 'orders')
            ->assertJsonPath('orders.0.id', $visible->id)
            ->assertJsonPath('orders.0.items.0.name', 'Comida');
    }

    public function test_status_transitions_are_sequential_and_retired_disappears(): void
    {
        $kitchen = User::factory()->create(['role' => User::ROLE_KITCHEN]);
        $order = $this->order('PENDING', true);

        $this->actingAs($kitchen)->patchJson(route('kitchen.orders.status', $order), ['status' => 'READY'])->assertUnprocessable();
        foreach (['IN_PREPARATION', 'READY', 'RETIRED'] as $status) {
            $this->actingAs($kitchen)->patchJson(route('kitchen.orders.status', $order), ['status' => $status])->assertOk();
        }

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'kitchen_status' => 'RETIRED']);
        $this->actingAs($kitchen)->getJson(route('kitchen.orders'))->assertJsonCount(0, 'orders');
    }

    public function test_waiter_notifications_are_scoped_by_waiter_ready_status_and_since(): void
    {
        $waiterRecord = WaiterModel::query()->create(['name' => 'Mozo Uno', 'employee_code' => 'M-1', 'is_active' => true]);
        $otherWaiter = WaiterModel::query()->create(['name' => 'Mozo Dos', 'employee_code' => 'M-2', 'is_active' => true]);
        $waiter = User::factory()->create(['role' => User::ROLE_MOZO, 'waiter_id' => $waiterRecord->id]);
        $ready = $this->order('READY', true);
        $ready->forceFill(['waiter_id' => $waiterRecord->id, 'kitchen_ready_at' => now()])->save();
        $other = $this->order('READY', true);
        $other->forceFill(['waiter_id' => $otherWaiter->id, 'kitchen_ready_at' => now()])->save();

        $this->actingAs($waiter)->getJson(route('tables.kitchen-notifications', ['since' => now()->subMinute()->toIso8601String()]))
            ->assertOk()->assertJsonCount(1, 'orders')->assertJsonPath('orders.0.id', $ready->id);
    }

    private function order(?string $kitchenStatus, bool $requiresKitchen, string $status = 'open'): OrderModel
    {
        static $number = 0;
        $table = TableModel::query()->create(['number' => ++$number]);
        $product = ProductModel::query()->create(['name' => $requiresKitchen ? 'Comida' : 'Bebida', 'category' => 'Test', 'price' => 10, 'is_active' => true, 'requires_kitchen' => $requiresKitchen]);
        $order = OrderModel::query()->create(['table_id' => $table->id, 'status' => $status, 'kitchen_status' => $kitchenStatus, 'subtotal' => 10, 'total' => 10, 'opened_at' => now(), 'sent_to_kitchen_at' => $kitchenStatus ? now() : null, 'closed_at' => $status === 'closed' ? now() : null]);
        OrderItemModel::query()->create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => 10, 'quantity' => 1, 'subtotal' => 10, 'requires_kitchen' => $requiresKitchen]);

        return $order;
    }
}
