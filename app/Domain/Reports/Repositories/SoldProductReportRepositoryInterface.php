<?php

namespace App\Domain\Reports\Repositories;

use App\Application\Reports\DTOs\SoldProductsReportQuery;

interface SoldProductReportRepositoryInterface
{
    /**
     * @return list<array{name: string, category: string, quantity: int, totalInCents: int}>
     */
    public function soldProducts(SoldProductsReportQuery $query): array;
}
