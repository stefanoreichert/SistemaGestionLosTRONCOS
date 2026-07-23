<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class DeleteProductUseCase
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function execute(int $id): void
    {
        $this->products->delete($id);
    }
}
