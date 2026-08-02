<?php

namespace Tests\Feature;

use App\Application\Table\Ports\TicketPrinterInterface;
use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderOwnershipAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_perform_every_order_mutation(): void
    {
        [$waiter, $owner] = $this->waiterUser('Propietario');
        $product = $this->product();
        $order = $this->order($product, (int) $waiter->id, 2);
        $this->mock(TicketPrinterInterface::class, fn (MockInterface $mock) => $mock->shouldReceive('print')->once());

        $this->actingAs($owner)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id])
            ->assertRedirect();
        $this->assertSame(3, $this->item($order, $product)->quantity);

        $this->post(route('tables.products.remove-unit', 1), ['product_id' => $product->id])
            ->assertRedirect();
        $this->assertSame(2, $this->item($order, $product)->quantity);

        $this->patch(route('tables.products.quantity', 1), [
            'product_id' => $product->id,
            'quantity' => 5,
        ])->assertRedirect();
        $this->assertSame(5, $this->item($order, $product)->quantity);

        $this->delete(route('tables.products.destroy', 1), ['product_id' => $product->id])
            ->assertRedirect();
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id, 'product_id' => $product->id]);

        $this->post(route('tables.products.search', 1), ['product_name' => $product->name])
            ->assertRedirect();
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id]);

        $this->post(route('tables.close', 1), ['payment_method' => 'cash'])->assertOk();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'closed',
            'waiter_id' => $waiter->id,
            'payment_method' => 'cash',
        ]);
    }

    public function test_other_waiter_can_view_but_every_manual_mutation_is_forbidden_without_changes(): void
    {
        [$ownerWaiter] = $this->waiterUser('Ana Propietaria');
        [, $otherUser] = $this->waiterUser('Bruno Ajeno');
        $product = $this->product();
        $order = $this->order($product, (int) $ownerWaiter->id, 2);
        $this->mock(TicketPrinterInterface::class, fn (MockInterface $mock) => $mock->shouldNotReceive('print'));

        $this->actingAs($otherUser)
            ->get(route('tables.show', 1))
            ->assertOk()
            ->assertSee('Esta mesa está siendo atendida por Ana Propietaria.')
            ->assertSee($product->name)
            ->assertDontSee('name="quantity"', false)
            ->assertDontSee('Cerrar y liberar mesa')
            ->assertDontSee('data-confirm-remove-product', false);

        $requests = [
            fn () => $this->post(route('tables.products.store', 1), ['product_id' => $product->id]),
            fn () => $this->post(route('tables.products.search', 1), ['product_name' => $product->name]),
            fn () => $this->post(route('tables.products.remove-unit', 1), ['product_id' => $product->id]),
            fn () => $this->patch(route('tables.products.quantity', 1), ['product_id' => $product->id, 'quantity' => 9]),
            fn () => $this->delete(route('tables.products.destroy', 1), ['product_id' => $product->id]),
            fn () => $this->post(route('tables.close', 1), ['payment_method' => 'card']),
        ];

        foreach ($requests as $request) {
            $request()->assertForbidden();
            $this->assertUnchanged($order, $product, (int) $ownerWaiter->id, 2);
        }
    }

    public function test_admin_can_operate_and_close_another_waiters_order_without_reassignment(): void
    {
        [$waiter] = $this->waiterUser('Mozo asignado');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = $this->product();
        $order = $this->order($product, (int) $waiter->id);
        $this->mock(TicketPrinterInterface::class, fn (MockInterface $mock) => $mock->shouldReceive('print')->once());

        $this->actingAs($admin)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id])
            ->assertRedirect();
        $this->post(route('tables.close', 1), ['payment_method' => 'transfer'])->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'waiter_id' => $waiter->id,
            'status' => 'closed',
        ]);
    }

    public function test_cashier_cannot_view_or_operate_tables(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $this->order($product, null);

        $this->actingAs($cashier)->get(route('tables.index'))->assertForbidden();
        $this->get(route('tables.show', 1))->assertForbidden();
        $this->post(route('tables.products.store', 1), ['product_id' => $product->id])->assertForbidden();
        $this->post(route('tables.close', 1), ['payment_method' => 'cash'])->assertForbidden();
    }

    public function test_inactive_account_cannot_access_or_operate_tables(): void
    {
        [$waiter, $user] = $this->waiterUser('Cuenta inactiva');
        $user->update(['is_active' => false]);
        $product = $this->product();
        $this->order($product, (int) $waiter->id);

        $this->actingAs($user)->get(route('tables.index'))->assertForbidden();
        $this->get(route('tables.show', 1))->assertForbidden();
        $this->post(route('tables.products.store', 1), ['product_id' => $product->id])->assertForbidden();
    }

    public function test_unassigned_order_keeps_phase_four_assignment_and_ignores_spoofed_waiter_id(): void
    {
        [$waiter, $user] = $this->waiterUser('Mozo real');
        [$spoofed] = $this->waiterUser('Mozo falsificado');
        $product = $this->product();
        $order = $this->order($product, null);

        $this->actingAs($user)->post(route('tables.products.store', 1), [
            'product_id' => $product->id,
            'waiter_id' => $spoofed->id,
        ])->assertRedirect();

        $this->assertSame($waiter->id, $order->fresh()->waiter_id);
    }

    public function test_admin_operates_unassigned_order_without_becoming_assigned(): void
    {
        $admin = User::factory()->create();
        $product = $this->product();
        $order = $this->order($product, null);

        $this->actingAs($admin)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id])
            ->assertRedirect();

        $this->assertNull($order->fresh()->waiter_id);
    }

    public function test_repository_rechecks_ownership_under_lock_and_rolls_back_stale_authorization(): void
    {
        [$firstWaiter] = $this->waiterUser('Primero');
        [$secondWaiter] = $this->waiterUser('Segundo');
        $product = $this->product();
        $order = $this->order($product, (int) $secondWaiter->id, 2);

        try {
            app(EloquentOrderRepository::class)->updateProductQuantity(
                1,
                (int) $product->id,
                9,
                false,
                (int) $firstWaiter->id,
            );
            $this->fail('La comprobación transaccional debía rechazar la propiedad obsoleta.');
        } catch (AuthorizationException $exception) {
            $this->assertSame('No tiene permiso para modificar este pedido.', $exception->getMessage());
        }

        $this->assertUnchanged($order, $product, (int) $secondWaiter->id, 2);
    }

    private function assertUnchanged(
        OrderModel $order,
        ProductModel $product,
        int $waiterId,
        int $quantity,
    ): void {
        $order->refresh();
        $item = $this->item($order, $product);

        $this->assertSame('open', $order->status);
        $this->assertSame($waiterId, $order->waiter_id);
        $this->assertNull($order->payment_method);
        $this->assertSame($quantity, $item->quantity);
        $this->assertSame('20.00', $order->total);
    }

    /** @return array{WaiterModel, User} */
    private function waiterUser(string $name): array
    {
        $waiter = WaiterModel::query()->create(['name' => $name, 'is_active' => true]);

        return [$waiter, User::factory()->waiter((int) $waiter->id)->create()];
    }

    private function product(): ProductModel
    {
        TableModel::query()->firstOrCreate(['number' => 1]);

        return ProductModel::query()->create([
            'name' => 'Sopa paraguaya',
            'category' => 'Entradas',
            'price' => 10,
            'is_active' => true,
        ]);
    }

    private function order(ProductModel $product, ?int $waiterId, int $quantity = 1): OrderModel
    {
        $order = OrderModel::query()->create([
            'table_id' => TableModel::query()->where('number', 1)->value('id'),
            'waiter_id' => $waiterId,
            'status' => 'open',
            'subtotal' => 10 * $quantity,
            'total' => 10 * $quantity,
            'opened_at' => now(),
        ]);

        OrderItemModel::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => 10,
            'subtotal' => 10 * $quantity,
        ]);

        return $order;
    }

    private function item(OrderModel $order, ProductModel $product): OrderItemModel
    {
        return OrderItemModel::query()
            ->where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
    }
}
