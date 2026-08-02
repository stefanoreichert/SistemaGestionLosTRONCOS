<?php

namespace App\Application\User\DTOs;

use App\Domain\User\Enums\UserRole;

final readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public ?string $phone,
        public string $email,
        public string $password,
        public UserRole $role,
    ) {
    }
}
