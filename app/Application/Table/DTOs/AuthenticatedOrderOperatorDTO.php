<?php

namespace App\Application\Table\DTOs;

final readonly class AuthenticatedOrderOperatorDTO
{
    public function __construct(
        public bool $isAdmin,
        public ?int $waiterId,
    ) {}
}
