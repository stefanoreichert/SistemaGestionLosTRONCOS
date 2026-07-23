<?php

namespace Tests\Feature;

use App\Application\Dashboard\UseCases\GetDashboardDataUseCase;
use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Infrastructure\Persistence\Repositories\EloquentProductRepository;
use App\Infrastructure\Persistence\Repositories\EloquentRestaurantTableRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_range_is_ensured_with_at_most_two_queries(): void
    {
        TableModel::query()->insert([
            ['number' => 1],
            ['number' => 25],
        ]);

        $repository = app(EloquentRestaurantTableRepository::class);
        $queries = $this->queriesDuring(
            fn () => $repository->ensureRange(1, 50),
        );

        $this->assertCount(2, $queries);
        $this->assertSame(50, TableModel::query()->count());

        $queries = $this->queriesDuring(
            fn () => $repository->ensureRange(1, 50),
        );

        $this->assertCount(1, $queries);
    }

    public function test_order_mutations_do_not_repeat_the_open_order_lookup(): void
    {
        TableModel::query()->create(['number' => 1]);
        $product = ProductModel::query()->create([
            'name' => 'Producto de prueba',
            'category' => 'Entradas',
            'price' => 1000,
        ]);
        $repository = app(EloquentOrderRepository::class);

        foreach ([
            fn () => $repository->addProduct(1, (int) $product->id),
            fn () => $repository->updateProductQuantity(1, (int) $product->id, 3),
            fn () => $repository->removeProductUnit(1, (int) $product->id),
            fn () => $repository->removeProduct(1, (int) $product->id),
        ] as $operation) {
            $queries = $this->queriesDuring($operation);
            $this->assertFalse($this->containsOrderLookupThroughTables($queries));
        }

        $repository->addProduct(1, (int) $product->id);
        $queries = $this->queriesDuring(
            fn () => $repository->closeByTableNumber(1, 'cash'),
        );

        $this->assertFalse($this->containsOrderLookupThroughTables($queries));
        $this->assertDatabaseHas('orders', [
            'table_id' => 1,
            'status' => 'closed',
            'payment_method' => 'cash',
        ]);
    }

    public function test_show_loads_the_requested_table_only_once(): void
    {
        app(EloquentRestaurantTableRepository::class)->ensureRange(1, 50);

        $queries = $this->queriesDuring(
            fn () => $this->get(route('tables.show', 1))->assertOk(),
        );

        $tableNumberQueries = array_filter(
            $queries,
            static fn (array $query): bool => str_contains(
                strtolower($query['query']),
                'from "tables" where "number" =',
            ),
        );

        $this->assertCount(1, $tableNumberQueries);
    }

    public function test_active_product_cache_is_invalidated_after_writes(): void
    {
        Cache::flush();
        $repository = app(EloquentProductRepository::class);
        $product = ProductModel::query()->create([
            'name' => 'Inicial',
            'category' => 'Entradas',
            'price' => 1000,
        ]);

        $firstLoad = $this->queriesDuring(fn () => $repository->active());
        $cachedLoad = $this->queriesDuring(fn () => $repository->active());

        $this->assertCount(1, $firstLoad);
        $this->assertCount(0, $cachedLoad);

        $entity = $repository->findById((int) $product->id);
        $this->assertNotNull($entity);
        $entity->rename('Actualizado');
        $repository->save($entity);

        $this->assertSame('Actualizado', $repository->active()[0]->name());

        $repository->delete((int) $product->id);
        $this->assertSame([], $repository->active());
    }

    public function test_dashboard_uses_consolidated_data_without_duplicate_queries(): void
    {
        app(EloquentRestaurantTableRepository::class)->ensureRange(1, 50);
        $product = ProductModel::query()->create([
            'name' => 'Producto',
            'category' => 'Entradas',
            'price' => 1000,
        ]);
        $order = OrderModel::query()->create([
            'table_id' => 1,
            'status' => 'closed',
            'subtotal' => 1000,
            'total' => 1000,
            'payment_method' => 'cash',
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

        $dashboard = app(GetDashboardDataUseCase::class);
        $data = null;
        $queries = $this->queriesDuring(
            function () use ($dashboard, &$data): void {
                $data = $dashboard->execute();
            },
        );

        $this->assertLessThanOrEqual(12, count($queries));
        $this->assertSame(50, $data['totalTables']);
        $this->assertSame(100000, $data['salesTodayInCents']);
        $this->assertSame(
            $data['dailySales']['totalInCents'],
            $data['salesTodayInCents'],
        );
    }

    /**
     * @return list<array{query: string, bindings: array, time: float}>
     */
    private function queriesDuring(callable $operation): array
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $operation();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return $queries;
    }

    /**
     * @param list<array{query: string, bindings: array, time: float}> $queries
     */
    private function containsOrderLookupThroughTables(array $queries): bool
    {
        foreach ($queries as $query) {
            $sql = strtolower($query['query']);

            if (str_contains($sql, 'from "orders"')
                && str_contains($sql, 'exists')
                && str_contains($sql, 'from "tables"')) {
                return true;
            }
        }

        return false;
    }
}
