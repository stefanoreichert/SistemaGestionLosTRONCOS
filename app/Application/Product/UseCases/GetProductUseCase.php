<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entities\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class GetProductUseCase
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function execute(int $id): ?Product
    {
        return $this->products->findById($id);
    }
}
