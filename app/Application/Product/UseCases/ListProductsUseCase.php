<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class ListProductsUseCase
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    /**
     * @return list<\App\Domain\Product\Entities\Product>
     */
    public function execute(?string $search = null): array
    {
        $term = trim((string) $search);

        return $term === ''
            ? $this->products->all()
            : $this->products->search($term);
    }
}
