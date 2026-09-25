<?php

namespace App\Domain\Reports\Repositories;

use App\Domain\Reports\Entities\DailyReportClosure;

interface DailyReportClosureRepositoryInterface
{
    public function closeCurrentPeriod(int $userId, string $idempotencyKey): DailyReportClosure;

    public function findById(int $id): ?DailyReportClosure;
}
