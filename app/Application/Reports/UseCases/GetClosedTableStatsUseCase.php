<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Reports\Repositories\DailySalesReportRepositoryInterface;

final readonly class GetClosedTableStatsUseCase
{
    public function __construct(private DailySalesReportRepositoryInterface $dailySales)
    {
    }

    /**
     * @return array{today: int, weekendAverage: float, monthlyAverage: float}
     */
    public function execute(): array
    {
        return $this->dailySales->closedTableStats();
    }
}
