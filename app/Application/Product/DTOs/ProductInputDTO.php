<?php

namespace App\Application\Product\DTOs;

final readonly class ProductInputDTO
{
    public function __construct(
        public string $name,
        public int $priceInCents,
        public string $category,
    ) {
    }
}
