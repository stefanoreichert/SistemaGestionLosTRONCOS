<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Reports\Entities\DailyReportClosure;
use App\Domain\Reports\Repositories\DailyReportClosureRepositoryInterface;

final readonly class CloseDailyReportUseCase
{
    public function __construct(private DailyReportClosureRepositoryInterface $closures) {}

    public function execute(int $userId, string $idempotencyKey): DailyReportClosure
    {
        return $this->closures->closeCurrentPeriod($userId, $idempotencyKey);
    }
}
