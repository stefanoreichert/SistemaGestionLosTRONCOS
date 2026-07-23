<?php

namespace App\Application\Tickets\DTOs;

final readonly class TicketFiltersDTO
{
    public function __construct(
        public ?string $from,
        public ?string $to,
        public ?int $tableNumber,
        public ?string $paymentMethod,
    ) {
    }
}
