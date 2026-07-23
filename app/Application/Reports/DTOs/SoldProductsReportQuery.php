<?php

namespace App\Application\Reports\DTOs;

final readonly class SoldProductsReportQuery
{
    public function __construct(
        public string $from,
        public string $to,
    ) {
    }
}
