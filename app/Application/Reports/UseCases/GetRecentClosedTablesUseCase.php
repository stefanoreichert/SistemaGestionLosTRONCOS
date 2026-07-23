<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Reports\Repositories\DailySalesReportRepositoryInterface;

final readonly class GetRecentClosedTablesUseCase
{
    public function __construct(private DailySalesReportRepositoryInterface $dailySales)
    {
    }

    /**
     * @return list<array{orderId: int, tableNumber: int, totalInCents: int, closedAt: string}>
     */
    public function execute(int $limit = 8): array
    {
        return $this->dailySales->recentClosedTables($limit);
    }
}
