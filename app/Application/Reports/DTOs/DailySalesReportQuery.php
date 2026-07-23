<?php

namespace App\Application\Reports\DTOs;

final readonly class DailySalesReportQuery
{
    public function __construct(public string $date)
    {
    }
}
