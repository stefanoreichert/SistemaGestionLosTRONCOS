<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class GetMonthlyReportUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $month, int $year): array
    {
        return $this->orders->monthlyReport($month, $year);
    }
}
