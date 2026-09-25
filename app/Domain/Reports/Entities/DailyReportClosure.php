<?php

namespace App\Domain\Reports\Entities;

final readonly class DailyReportClosure
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        private int $id,
        private string $periodStartedAt,
        private string $closedAt,
        private int $closedByUserId,
        private array $report,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function periodStartedAt(): string
    {
        return $this->periodStartedAt;
    }

    public function closedAt(): string
    {
        return $this->closedAt;
    }

    public function closedByUserId(): int
    {
        return $this->closedByUserId;
    }

    /** @return array<string, mixed> */
    public function report(): array
    {
        return $this->report;
    }
}
