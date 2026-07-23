<?php

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\ProductInputDTO;
use App\Domain\Product\Entities\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class CreateProductUseCase
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function execute(ProductInputDTO $dto): Product
    {
        return $this->products->save(new Product(
            id: null,
            name: $dto->name,
            priceInCents: $dto->priceInCents,
            category: $dto->category,
        ));
    }
}
