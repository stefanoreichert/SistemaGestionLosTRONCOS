<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_a_product_can_be_deactivated_without_being_deleted_and_reactivated(): void
    {
        $product = $this->product();

        $this->patch(route('products.availability', $product), ['is_active' => false])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', 'Producto desactivado correctamente.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
        $this->assertSame(1, ProductModel::query()->count());

        $this->patch(route('products.availability', $product), ['is_active' => true])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', 'Producto activado correctamente.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => true,
        ]);
    }

    public function test_the_product_destroy_route_no_longer_exists(): void
    {
        $product = $this->product();

        $this->delete('/products/'.$product->id)->assertMethodNotAllowed();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_changing_availability_for_an_unknown_product_returns_not_found(): void
    {
        $this->patch(route('products.availability', 999999), [
            'is_active' => false,
        ])->assertNotFound();
    }

    public function test_inactive_products_are_not_offered_for_a_table(): void
    {
        TableModel::query()->create(['number' => 1]);
        $active = $this->product('Producto activo');
        $inactive = $this->product('Producto inactivo', false);

        $response = $this->get(route('tables.show', 1))->assertOk();

        $response->assertSee($active->name);
        $response->assertDontSee($inactive->name);
    }

    public function test_an_inactive_product_cannot_be_added_through_a_manual_request(): void
    {
        TableModel::query()->create(['number' => 1]);
        $inactive = $this->product('Producto inactivo', false);

        $this->post(route('tables.products.store', 1), [
            'product_id' => $inactive->id,
        ])->assertNotFound();

        $this->assertDatabaseMissing('order_items', [
            'product_id' => $inactive->id,
        ]);
    }

    public function test_an_open_order_keeps_an_already_added_product_after_deactivation(): void
    {
        TableModel::query()->create(['number' => 1]);
        $product = $this->product('Producto del pedido');
        app(EloquentOrderRepository::class)->addProduct(1, (int) $product->id);

        $product->is_active = false;
        $product->save();

        $response = $this->get(route('tables.show', 1))->assertOk();

        $this->assertSame(1, substr_count($response->getContent(), 'Producto del pedido'));
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_closed_orders_and_tickets_keep_inactive_products(): void
    {
        $table = TableModel::query()->create(['number' => 1]);
        $product = $this->product('Producto histórico', false);
        $order = OrderModel::query()->create([
            'table_id' => $table->id,
            'status' => 'closed',
            'subtotal' => 1000,
            'total' => 1000,
            'payment_method' => 'cash',
            'ticket_number' => 'T-0001',
            'opened_at' => now()->subHour(),
            'closed_at' => now(),
        ]);
        OrderItemModel::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        $ticket = $this->get(route('tickets.show', $order->id))->assertOk();
        $this->assertStringContainsString('PRODUCTO', $ticket->getContent());
        $this->assertStringContainsString('HIST', $ticket->getContent());

        $this->get(route('reports.sold-products', ['period' => 'today']))
            ->assertOk()
            ->assertSee('Producto histórico');
    }

    private function product(string $name = 'Producto', bool $isActive = true): ProductModel
    {
        return ProductModel::query()->create([
            'name' => $name,
            'category' => 'Entradas',
            'price' => 1000,
            'is_active' => $isActive,
        ]);
    }
}
