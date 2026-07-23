<?php

namespace App\Application\Table\DTOs;

final readonly class UpdateProductQuantityDTO
{
    public function __construct(
        public int $tableNumber,
        public int $productId,
        public int $quantity,
    ) {
    }
}
