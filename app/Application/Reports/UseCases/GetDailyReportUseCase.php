<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class GetDailyReportUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $date): array
    {
        return $this->orders->dailyReport($date);
    }
}
