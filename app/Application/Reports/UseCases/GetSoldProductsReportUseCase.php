<?php

namespace App\Application\Reports\UseCases;

use App\Application\Reports\DTOs\SoldProductsReportQuery;
use App\Domain\Reports\Repositories\SoldProductReportRepositoryInterface;

final readonly class GetSoldProductsReportUseCase
{
    public function __construct(private SoldProductReportRepositoryInterface $soldProducts)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(SoldProductsReportQuery $query): array
    {
        $products = $this->soldProducts->soldProducts($query);

        return [
            'from' => $query->from,
            'to' => $query->to,
            'products' => $products,
            'totalQuantity' => array_sum(array_column($products, 'quantity')),
            'totalInCents' => array_sum(array_column($products, 'totalInCents')),
        ];
    }
}
