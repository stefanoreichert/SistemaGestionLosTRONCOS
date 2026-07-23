<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class ListActiveProductsUseCase
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function execute(): array
    {
        return $this->products->active();
    }
}
