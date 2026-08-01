<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWaiterAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_product_assigns_the_authenticated_waiter(): void
    {
        [$waiter, $user, $product] = $this->waiterUserAndProduct();

        $this->actingAs($user)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id])
            ->assertRedirect(route('tables.show', 1));

        $this->assertDatabaseHas('orders', [
            'table_id' => 1,
            'status' => 'open',
            'waiter_id' => $waiter->id,
        ]);
    }

    public function test_another_waiter_cannot_replace_or_modify_the_assigned_order(): void
    {
        [$firstWaiter, $firstUser, $product] = $this->waiterUserAndProduct();
        [$secondWaiter, $secondUser] = $this->createWaiterUser('Segundo mozo');

        $this->actingAs($firstUser)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id]);
        $this->actingAs($secondUser)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id])
            ->assertForbidden();

        $order = OrderModel::query()->where('table_id', 1)->where('status', 'open')->firstOrFail();

        $this->assertSame($firstWaiter->id, $order->waiter_id);
        $this->assertNotSame($secondWaiter->id, $order->waiter_id);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_admin_is_not_assigned_and_a_waiter_can_claim_the_unassigned_order_later(): void
    {
        [, $waiterUser, $product] = $this->waiterUserAndProduct();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'waiter_id' => null]);

        $this->actingAs($admin)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id]);

        $this->assertNull($this->openOrder()->waiter_id);

        $this->actingAs($waiterUser)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id]);

        $this->assertSame($waiterUser->waiter_id, $this->openOrder()->waiter_id);
    }

    public function test_closed_order_keeps_its_waiter(): void
    {
        [$waiter, $user, $product] = $this->waiterUserAndProduct();

        $this->actingAs($user)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id]);

        app(EloquentOrderRepository::class)->closeByTableNumber(1, 'cash', true, null);

        $this->assertDatabaseHas('orders', [
            'table_id' => 1,
            'status' => 'closed',
            'waiter_id' => $waiter->id,
        ]);
    }

    public function test_historical_unassigned_order_loads_and_displays_without_errors(): void
    {
        $admin = User::factory()->create();
        $product = $this->createProduct();
        $order = $this->createOrderWithItem($product, null);

        $this->actingAs($admin)
            ->get(route('tables.show', 1))
            ->assertOk()
            ->assertSee('Mozo no asignado');

        $this->assertNull($order->fresh()->waiter_id);
    }

    public function test_occupied_table_displays_the_assigned_waiter_name(): void
    {
        [$waiter, $user, $product] = $this->waiterUserAndProduct('Ana Gómez');
        $this->createOrderWithItem($product, (int) $waiter->id);

        $this->actingAs($user)
            ->get(route('tables.index'))
            ->assertOk()
            ->assertSee('Atiende: Ana Gómez');
    }

    public function test_free_tables_do_not_display_waiter_information(): void
    {
        $admin = User::factory()->create();
        TableModel::query()->create(['number' => 1]);

        $this->actingAs($admin)
            ->get(route('tables.index'))
            ->assertOk()
            ->assertDontSee('Atiende:')
            ->assertDontSee('Mozo no asignado');
    }

    public function test_inactive_waiter_cannot_start_a_new_assignment(): void
    {
        [$waiter, $user, $product] = $this->waiterUserAndProduct();
        $waiter->update(['is_active' => false]);

        $this->actingAs($user)
            ->post(route('tables.products.store', 1), ['product_id' => $product->id])
            ->assertForbidden();

        $this->assertDatabaseMissing('orders', ['table_id' => 1]);
        $this->assertDatabaseMissing('order_items', ['product_id' => $product->id]);
    }

    public function test_waiter_id_cannot_be_spoofed_from_a_manual_request(): void
    {
        [$authenticatedWaiter, $user, $product] = $this->waiterUserAndProduct();
        [$spoofedWaiter] = $this->createWaiterUser('Mozo falsificado');

        $this->actingAs($user)->post(route('tables.products.store', 1), [
            'product_id' => $product->id,
            'waiter_id' => $spoofedWaiter->id,
        ]);

        $this->assertSame($authenticatedWaiter->id, $this->openOrder()->waiter_id);

        app(EloquentOrderRepository::class)->closeByTableNumber(1, 'cash', true, null);
        TableModel::query()->where('number', 1)->update(['number' => 2]);
        TableModel::query()->create(['number' => 1]);

        $admin = User::factory()->create();
        $this->actingAs($admin)->post(route('tables.products.store', 1), [
            'product_id' => $product->id,
            'waiter_id' => $spoofedWaiter->id,
        ]);

        $this->assertNull($this->openOrder()->waiter_id);
    }

    public function test_opening_a_table_does_not_assign_the_authenticated_waiter(): void
    {
        [, $user] = $this->waiterUserAndProduct();

        $this->actingAs($user)
            ->get(route('tables.show', 1))
            ->assertOk();

        $this->assertNull($this->openOrder()->waiter_id);
    }

    /**
     * @return array{WaiterModel, User, ProductModel}
     */
    private function waiterUserAndProduct(string $name = 'Mozo activo'): array
    {
        TableModel::query()->firstOrCreate(['number' => 1]);
        [$waiter, $user] = $this->createWaiterUser($name);

        return [$waiter, $user, $this->createProduct()];
    }

    /**
     * @return array{WaiterModel, User}
     */
    private function createWaiterUser(string $name): array
    {
        $waiter = WaiterModel::query()->create([
            'name' => $name,
            'is_active' => true,
        ]);

        return [$waiter, User::factory()->waiter((int) $waiter->id)->create()];
    }

    private function createProduct(): ProductModel
    {
        return ProductModel::query()->create([
            'name' => 'Producto de prueba',
            'category' => 'Entradas',
            'price' => 10,
            'is_active' => true,
        ]);
    }

    private function createOrderWithItem(ProductModel $product, ?int $waiterId): OrderModel
    {
        $table = TableModel::query()->firstOrCreate(['number' => 1]);
        $order = OrderModel::query()->create([
            'table_id' => $table->id,
            'waiter_id' => $waiterId,
            'status' => 'open',
            'subtotal' => 10,
            'total' => 10,
            'opened_at' => now(),
        ]);

        OrderItemModel::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10,
            'subtotal' => 10,
        ]);

        return $order;
    }

    private function openOrder(): OrderModel
    {
        return OrderModel::query()->where('status', 'open')->whereHas('table', function ($query): void {
            $query->where('number', 1);
        })->firstOrFail();
    }
}
