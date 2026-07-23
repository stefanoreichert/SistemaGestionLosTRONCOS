<?php

namespace App\Application\Dashboard\UseCases;

use App\Application\Reports\DTOs\DailySalesReportQuery;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Reports\Repositories\DailySalesReportRepositoryInterface;
use App\Domain\Table\Repositories\OrderRepositoryInterface;
use App\Domain\Table\Repositories\RestaurantTableRepositoryInterface;

final readonly class GetDashboardDataUseCase
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private RestaurantTableRepositoryInterface $tables,
        private OrderRepositoryInterface $orders,
        private DailySalesReportRepositoryInterface $dailySales,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $freeTables = $this->tables->countFree();
        $occupiedTables = $this->tables->countOccupied();
        $totalTables = $freeTables + $occupiedTables;
        $dailySales = $this->dailySales->dailySales(
            new DailySalesReportQuery(now()->toDateString()),
        );

        return [
            'totalProducts' => $this->products->count(),
            'totalTables' => $totalTables,
            'freeTables' => $freeTables,
            'occupiedTables' => $occupiedTables,
            'occupancyPercentage' => $totalTables > 0
                ? (int) round(($occupiedTables / $totalTables) * 100)
                : 0,
            'openOrders' => $this->orders->openCount(),
            'salesTodayInCents' => $this->orders->salesTodayInCents(),
            'salesMonthInCents' => $this->orders->salesMonthInCents(),
            'productsSoldToday' => $this->orders->productsSoldToday(),
            'dailySales' => $dailySales,
            'closedTableStats' => $this->dailySales->closedTableStats(),
            'tables' => $this->tables->allWithOpenOrder(),
            'recentOrders' => $this->orders->recentClosed(8),
            'recentClosedTables' => $this->dailySales->recentClosedTables(8),
            'featuredProducts' => array_slice($this->products->all(), 0, 6),
        ];
    }
}
