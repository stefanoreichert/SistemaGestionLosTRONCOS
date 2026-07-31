<?php

namespace App\Application\Waiter\DTOs;

final readonly class WaiterInputDTO
{
    public function __construct(
        public string $name,
        public ?string $employeeCode,
        public ?string $phone,
    ) {
    }
}
