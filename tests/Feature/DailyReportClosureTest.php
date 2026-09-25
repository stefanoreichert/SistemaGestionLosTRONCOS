<?php

namespace Tests\Feature;

use App\Application\Reports\UseCases\GetDailyReportUseCase;
use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Infrastructure\Persistence\Repositories\EloquentTicketRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class DailyReportClosureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private TableModel $table;

    private ProductModel $product;

    protected function setUp(): void
    {
        parent::setUp();

        DB::connection()->getPdo()->sqliteCreateFunction(
            'HOUR',
            static fn (?string $value): int => $value === null ? 0 : (int) date('G', strtotime($value)),
        );

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->table = TableModel::query()->create(['number' => 1]);
        $this->product = ProductModel::query()->create([
            'name' => 'Hamburguesa completa',
            'category' => 'Comidas',
            'price' => 15000,
        ]);
    }

    public function test_closing_the_current_period_preserves_sales_and_resets_the_daily_report(): void
    {
        $cashOrder = $this->closedOrder('cash', 15000, 2);
        $cardOrder = $this->closedOrder('card', 10000, 1);
        $token = (string) Str::uuid();

        $before = app(GetDailyReportUseCase::class)->execute(now()->toDateString());
        $this->assertSame(2500000, $before['totalSoldInCents']);
        $this->assertSame(2, $before['ordersCount']);
        $this->assertSame(3, $before['soldProductsCount']);
        $this->assertSame(1500000, $before['cashTotalInCents']);
        $this->assertSame(1000000, $before['cardTotalInCents']);

        $response = $this->actingAs($this->admin)->post(route('reports.daily-closures.store'), [
            'idempotency_key' => $token,
        ]);

        $closureId = (int) DB::table('daily_report_closures')->value('id');
        $response->assertRedirect(route('reports.daily-closures.show', $closureId));
        $this->assertDatabaseCount('daily_report_closures', 1);
        $this->assertDatabaseHas('orders', ['id' => $cashOrder->id, 'daily_report_closure_id' => $closureId]);
        $this->assertDatabaseHas('orders', ['id' => $cardOrder->id, 'daily_report_closure_id' => $closureId]);
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('order_items', 2);

        $after = app(GetDailyReportUseCase::class)->execute(now()->toDateString());
        $this->assertSame(0, $after['totalSoldInCents']);
        $this->assertSame(0, $after['ordersCount']);
        $this->assertSame(0, $after['usedTablesCount']);
        $this->assertSame(0, $after['soldProductsCount']);

        $this->get(route('reports.daily-closures.show', $closureId))
            ->assertOk()
            ->assertSee('Los Troncos Resto Bar')
            ->assertSee('Fecha y hora del cierre')
            ->assertSee('Hamburguesa completa')
            ->assertDontSee('sidebar');
    }

    public function test_a_new_sale_after_closing_starts_a_new_period(): void
    {
        $this->closedOrder('cash', 15000, 1);
        $this->actingAs($this->admin)->post(route('reports.daily-closures.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ])->assertRedirect();

        $this->closedOrder('transfer', 8000, 2);
        $report = app(GetDailyReportUseCase::class)->execute(now()->toDateString());

        $this->assertSame(800000, $report['totalSoldInCents']);
        $this->assertSame(1, $report['ordersCount']);
        $this->assertSame(2, $report['soldProductsCount']);
        $this->assertSame(800000, $report['transferTotalInCents']);
    }

    public function test_repeating_the_same_request_is_idempotent(): void
    {
        $this->closedOrder('cash', 10000, 1);
        $token = (string) Str::uuid();

        $first = $this->actingAs($this->admin)->post(route('reports.daily-closures.store'), [
            'idempotency_key' => $token,
        ]);
        $second = $this->post(route('reports.daily-closures.store'), [
            'idempotency_key' => $token,
        ]);

        $this->assertSame($first->headers->get('Location'), $second->headers->get('Location'));
        $this->assertDatabaseCount('daily_report_closures', 1);
    }

    public function test_empty_period_is_not_closed(): void
    {
        $this->actingAs($this->admin)
            ->post(route('reports.daily-closures.store'), ['idempotency_key' => (string) Str::uuid()])
            ->assertRedirect(route('reports.daily'))
            ->assertSessionHas('warning', 'No hay operaciones nuevas para cerrar.');

        $this->assertDatabaseCount('daily_report_closures', 0);
    }

    public function test_monthly_reports_and_tickets_keep_closed_orders_after_daily_closure(): void
    {
        $order = $this->closedOrder('cash', 12000, 1);
        $this->actingAs($this->admin)->post(route('reports.daily-closures.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ])->assertRedirect();

        $monthly = app(EloquentOrderRepository::class)->monthlyReport(now()->month, now()->year);
        $ticket = app(EloquentTicketRepository::class)->findClosedById((int) $order->id);

        $this->assertSame(1200000, $monthly['billingInCents']);
        $this->assertSame(1, $monthly['ordersCount']);
        $this->assertNotNull($ticket);
        $this->assertSame(1200000, $ticket->totalInCents());
    }

    public function test_guests_and_unauthorized_roles_cannot_close_a_daily_report(): void
    {
        $payload = ['idempotency_key' => (string) Str::uuid()];
        $this->post(route('reports.daily-closures.store'), $payload)
            ->assertRedirect(route('login'));

        $mozo = User::factory()->create(['role' => User::ROLE_MOZO]);
        $this->actingAs($mozo)
            ->post(route('reports.daily-closures.store'), $payload)
            ->assertForbidden();
    }

    private function closedOrder(string $paymentMethod, int $total, int $quantity): OrderModel
    {
        $order = OrderModel::query()->create([
            'table_id' => $this->table->id,
            'status' => 'closed',
            'subtotal' => $total,
            'total' => $total,
            'payment_method' => $paymentMethod,
            'opened_at' => now()->subHour(),
            'closed_at' => now(),
        ]);

        OrderItemModel::query()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => $quantity,
            'unit_price' => $total / $quantity,
            'subtotal' => $total,
        ]);

        return $order;
    }
}
