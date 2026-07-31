<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entities\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class SetProductAvailabilityUseCase
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function execute(int $id, bool $isActive): Product
    {
        $product = $this->products->getById($id);

        if ($isActive) {
            $product->activate();
        } else {
            $product->deactivate();
        }

        return $this->products->save($product);
    }
}
