<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Reports\DTOs\SoldProductsReportQuery;
use App\Domain\Reports\Repositories\SoldProductReportRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use Illuminate\Support\Facades\DB;

final readonly class EloquentSoldProductReportRepository implements SoldProductReportRepositoryInterface
{
    public function soldProducts(SoldProductsReportQuery $query): array
    {
        return OrderItemModel::query()
            ->select(
                'products.id',
                'products.name',
                'products.category',
                DB::raw('SUM(order_items.quantity) as sold_quantity'),
                DB::raw('SUM(order_items.subtotal) as sold_total'),
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'closed')
            ->whereBetween('orders.closed_at', [$query->from.' 00:00:00', $query->to.' 23:59:59'])
            ->groupBy('products.id', 'products.name', 'products.category')
            ->orderByDesc('sold_quantity')
            ->get()
            ->map(fn ($row): array => [
                'name' => (string) $row->name,
                'category' => (string) $row->category,
                'quantity' => (int) $row->sold_quantity,
                'totalInCents' => (int) round(((float) $row->sold_total) * 100),
            ])
            ->all();
    }
}
