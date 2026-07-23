<?php

namespace App\Application\Table\DTOs;

final readonly class CloseTableOrderDTO
{
    public function __construct(
        public int $tableNumber,
        public string $paymentMethod,
    ) {
    }
}
