<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final readonly class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EloquentOrderMapper $mapper)
    {
    }

    public function openForTableNumber(int $tableNumber): Order
    {
        return DB::transaction(function () use ($tableNumber): Order {
            return $this->freshOrder($this->openOrderModel($tableNumber));
        });
    }

    public function findOpenByTableNumber(int $tableNumber): ?Order
    {
        $order = OrderModel::query()
            ->with(['table', 'items.product'])
            ->where('status', 'open')
            ->whereHas('table', fn ($query) => $query->where('number', $tableNumber))
            ->first();

        return $order instanceof OrderModel ? $this->mapper->toEntity($order) : null;
    }

    public function addProduct(int $tableNumber, int $productId): Order
    {
        return DB::transaction(function () use ($tableNumber, $productId): Order {
            $order = $this->openOrderModel($tableNumber);
            $product = ProductModel::query()->findOrFail($productId);
            $item = OrderItemModel::query()
                ->where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->first();

            $unitPrice = (float) $product->price;

            if ($item instanceof OrderItemModel) {
                $item->quantity++;
                $item->subtotal = $item->quantity * (float) $item->unit_price;
                $item->save();
            } else {
                OrderItemModel::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice,
                ]);
            }

            $this->recalculate($order);

            return $this->freshOrder($order);
        });
    }

    public function removeProductUnit(int $tableNumber, int $productId): Order
    {
        return DB::transaction(function () use ($tableNumber, $productId): Order {
            $order = $this->openOrderModel($tableNumber);
            $item = $this->findItem($order, $productId);

            if ($item->quantity <= 1) {
                $item->delete();
            } else {
                $item->quantity--;
                $item->subtotal = $item->quantity * (float) $item->unit_price;
                $item->save();
            }

            $this->recalculate($order);

            return $this->freshOrder($order);
        });
    }

    public function updateProductQuantity(int $tableNumber, int $productId, int $quantity): Order
    {
        return DB::transaction(function () use ($tableNumber, $productId, $quantity): Order {
            $order = $this->openOrderModel($tableNumber);
            $item = $this->findItem($order, $productId);

            $item->quantity = $quantity;
            $item->subtotal = $item->quantity * (float) $item->unit_price;
            $item->save();

            $this->recalculate($order);

            return $this->freshOrder($order);
        });
    }

    public function removeProduct(int $tableNumber, int $productId): Order
    {
        return DB::transaction(function () use ($tableNumber, $productId): Order {
            $order = $this->openOrderModel($tableNumber);
            $this->findItem($order, $productId)->delete();
            $this->recalculate($order);

            return $this->freshOrder($order);
        });
    }

    public function closeByTableNumber(int $tableNumber, string $paymentMethod): Order
    {
        return DB::transaction(function () use ($tableNumber, $paymentMethod): Order {
            $order = $this->openOrderModel($tableNumber);
            $this->recalculate($order);
            $order->status = 'closed';
            $order->payment_method = $paymentMethod;
            $order->closed_at = now();
            $order->save();

            return $this->freshOrder($order);
        });
    }

    public function openCount(): int
    {
        return OrderModel::query()
            ->where('status', 'open')
            ->whereHas('items')
            ->count();
    }

    public function salesTodayInCents(): int
    {
        [$start, $end] = $this->dayRange(now()->toDateString());

        return (int) round(OrderModel::query()
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
            ->sum('total') * 100);
    }

    public function salesMonthInCents(): int
    {
        [$start, $end] = $this->monthRange(now()->month, now()->year);

        return (int) round(OrderModel::query()
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
            ->sum('total') * 100);
    }

    public function productsSoldToday(): int
    {
        [$start, $end] = $this->dayRange(now()->toDateString());

        return (int) OrderItemModel::query()
            ->whereHas('order', fn ($query) => $query
                ->where('status', 'closed')
                ->whereBetween('closed_at', [$start, $end]))
            ->sum('quantity');
    }

    public function recentClosed(int $limit): array
    {
        return OrderModel::query()
            ->with(['table', 'items.product'])
            ->where('status', 'closed')
            ->latest('closed_at')
            ->limit($limit)
            ->get()
            ->map(fn (OrderModel $order): Order => $this->mapper->toEntity($order))
            ->all();
    }

    public function dailyReport(string $date): array
    {
        $orders = $this->closedOrdersForDate($date);
        $total = (int) round((float) $orders->sum('total') * 100);
        $orderIds = $orders->pluck('id')->all();

        return [
            'date' => $date,
            'totalSoldInCents' => $total,
            'ordersCount' => $orders->count(),
            'usedTablesCount' => $orders->pluck('table_id')->unique()->count(),
            'topProduct' => $this->topProduct($orderIds),
            'topCategory' => $this->topCategory($orderIds),
            'averagePerTableInCents' => $this->average($total, $orders->pluck('table_id')->unique()->count()),
            'averagePerTicketInCents' => $this->average($total, $orders->count()),
            'salesByHour' => $this->salesByHour($date),
            'orders' => $orders->map(fn (OrderModel $order): Order => $this->mapper->toEntity($order))->all(),
            'cashTotalInCents' => $total,
            'grandTotalInCents' => $total,
        ];
    }

    public function monthlyReport(int $month, int $year): array
    {
        [$start, $end] = $this->monthRange($month, $year);

        $orders = OrderModel::query()
            ->with(['table', 'items.product'])
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
            ->get();
        $total = (int) round((float) $orders->sum('total') * 100);
        $orderIds = $orders->pluck('id')->all();
        $days = max(1, $orders->pluck(fn (OrderModel $order): string => $order->closed_at->toDateString())->unique()->count());

        return [
            'month' => $month,
            'year' => $year,
            'billingInCents' => $total,
            'ordersCount' => $orders->count(),
            'soldProductsCount' => $this->soldProductsCount($orderIds),
            'topProduct' => $this->topProduct($orderIds),
            'topCategory' => $this->topCategory($orderIds),
            'dailyAverageInCents' => $this->average($total, $days),
            'averagePerTicketInCents' => $this->average($total, $orders->count()),
            'salesByDay' => $this->salesByDay($month, $year),
            'productRanking' => $this->productRanking($orderIds),
        ];
    }

    private function findTable(int $number): TableModel
    {
        return TableModel::query()->where('number', $number)->firstOrFail();
    }

    private function openOrderModel(int $tableNumber): OrderModel
    {
        $table = $this->findTable($tableNumber);

        return OrderModel::query()->firstOrCreate(
            ['table_id' => $table->id, 'status' => 'open'],
            ['subtotal' => 0, 'total' => 0, 'opened_at' => now(), 'closed_at' => null],
        );
    }

    private function freshOrder(OrderModel $order): Order
    {
        $order = OrderModel::query()->with(['table', 'items.product'])->findOrFail($order->id);

        return $this->mapper->toEntity($order);
    }

    private function findItem(OrderModel $order, int $productId): OrderItemModel
    {
        return OrderItemModel::query()
            ->where('order_id', $order->id)
            ->where('product_id', $productId)
            ->firstOrFail();
    }

    private function recalculate(OrderModel $order): void
    {
        $total = (float) OrderItemModel::query()->where('order_id', $order->id)->sum('subtotal');
        $order->subtotal = $total;
        $order->total = $total;
        $order->save();
    }

    private function closedOrdersForDate(string $date)
    {
        [$start, $end] = $this->dayRange($date);

        return OrderModel::query()
            ->with(['table', 'items.product'])
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
            ->get();
    }

    private function average(int $totalInCents, int $divisor): int
    {
        return $divisor > 0 ? (int) round($totalInCents / $divisor) : 0;
    }

    private function soldProductsCount(array $orderIds): int
    {
        return (int) OrderItemModel::query()->whereIn('order_id', $orderIds)->sum('quantity');
    }

    private function topProduct(array $orderIds): ?string
    {
        $row = OrderItemModel::query()
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold'))
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('order_items.order_id', $orderIds)
            ->groupBy('products.name')
            ->orderByDesc('sold')
            ->first();

        return $row?->name;
    }

    private function topCategory(array $orderIds): ?string
    {
        $row = OrderItemModel::query()
            ->select('products.category', DB::raw('SUM(order_items.quantity) as sold'))
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('order_items.order_id', $orderIds)
            ->groupBy('products.category')
            ->orderByDesc('sold')
            ->first();

        return $row?->category;
    }

    private function salesByHour(string $date): array
    {
        [$start, $end] = $this->dayRange($date);

        return OrderModel::query()
            ->select(DB::raw('HOUR(closed_at) as hour'), DB::raw('SUM(total) as total'))
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn ($row): array => [
                'label' => str_pad((string) $row->hour, 2, '0', STR_PAD_LEFT).':00',
                'totalInCents' => (int) round(((float) $row->total) * 100),
            ])
            ->all();
    }

    private function salesByDay(int $month, int $year): array
    {
        [$start, $end] = $this->monthRange($month, $year);

        return OrderModel::query()
            ->select(DB::raw('DATE(closed_at) as day'), DB::raw('SUM(total) as total'))
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row): array => [
                'label' => (string) $row->day,
                'totalInCents' => (int) round(((float) $row->total) * 100),
            ])
            ->all();
    }

    /**
     * @return array{CarbonImmutable, CarbonImmutable}
     */
    private function dayRange(string $date): array
    {
        $day = CarbonImmutable::parse($date);

        return [$day->startOfDay(), $day->endOfDay()];
    }

    /**
     * @return array{CarbonImmutable, CarbonImmutable}
     */
    private function monthRange(int $month, int $year): array
    {
        $start = CarbonImmutable::create($year, $month, 1)->startOfDay();

        return [$start, $start->endOfMonth()->endOfDay()];
    }

    private function productRanking(array $orderIds): array
    {
        return OrderItemModel::query()
            ->select('products.name', 'products.category', DB::raw('SUM(order_items.quantity) as sold'), DB::raw('SUM(order_items.subtotal) as total'))
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('order_items.order_id', $orderIds)
            ->groupBy('products.name', 'products.category')
            ->orderByDesc('sold')
            ->get()
            ->map(fn ($row): array => [
                'name' => (string) $row->name,
                'category' => (string) $row->category,
                'sold' => (int) $row->sold,
                'totalInCents' => (int) round(((float) $row->total) * 100),
            ])
            ->all();
    }
}
