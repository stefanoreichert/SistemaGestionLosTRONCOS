<?php

namespace App\Application\Table\DTOs;

final readonly class RemoveProductFromOrderDTO
{
    public function __construct(
        public int $tableNumber,
        public int $productId,
    ) {
    }
}
