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
        $tables = $this->tables->allWithOpenOrder();
        $totalTables = count($tables);
        $occupiedTables = count(array_filter(
            $tables,
            static fn ($table): bool => $table->isOccupied(),
        ));
        $freeTables = $totalTables - $occupiedTables;
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
            'openOrders' => $occupiedTables,
            'salesTodayInCents' => $dailySales['totalInCents'],
            'salesMonthInCents' => $this->orders->salesMonthInCents(),
            'productsSoldToday' => $this->orders->productsSoldToday(),
            'dailySales' => $dailySales,
            'closedTableStats' => $this->dailySales->closedTableStats($dailySales['closedOrdersCount']),
            'tables' => $tables,
            'recentClosedTables' => $this->dailySales->recentClosedTables(8),
        ];
    }
}
