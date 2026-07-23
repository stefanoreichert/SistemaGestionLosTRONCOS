<?php

namespace App\Domain\Reports\Repositories;

use App\Application\Reports\DTOs\DailySalesReportQuery;

interface DailySalesReportRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function dailySales(DailySalesReportQuery $query): array;

    /**
     * @return list<array{orderId: int, tableNumber: int, totalInCents: int, closedAt: string}>
     */
    public function recentClosedTables(int $limit): array;

    /**
     * @return array{today: int, weekendAverage: float, monthlyAverage: float}
     */
    public function closedTableStats(): array;
}
