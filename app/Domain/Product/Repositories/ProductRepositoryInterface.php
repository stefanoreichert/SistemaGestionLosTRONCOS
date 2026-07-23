<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\Entities\Product;

interface ProductRepositoryInterface
{
    /**
     * @return list<Product>
     */
    public function all(): array;

    /**
     * @return list<Product>
     */
    public function search(string $term): array;

    /**
     * @return list<Product>
     */
    public function active(): array;

    public function count(): int;

    public function findById(int $id): ?Product;

    public function save(Product $product): Product;

    public function delete(int $id): void;
}
