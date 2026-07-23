<?php

namespace App\Application\Reports\UseCases;

use App\Application\Reports\DTOs\DailySalesReportQuery;
use App\Domain\Reports\Repositories\DailySalesReportRepositoryInterface;

final readonly class GetDailySalesReportUseCase
{
    public function __construct(private DailySalesReportRepositoryInterface $dailySales)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(DailySalesReportQuery $query): array
    {
        return $this->dailySales->dailySales($query);
    }
}
