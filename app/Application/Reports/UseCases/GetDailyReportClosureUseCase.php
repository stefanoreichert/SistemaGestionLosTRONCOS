<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Reports\Entities\DailyReportClosure;
use App\Domain\Reports\Repositories\DailyReportClosureRepositoryInterface;

final readonly class GetDailyReportClosureUseCase
{
    public function __construct(private DailyReportClosureRepositoryInterface $closures) {}

    public function execute(int $id): ?DailyReportClosure
    {
        return $this->closures->findById($id);
    }
}
