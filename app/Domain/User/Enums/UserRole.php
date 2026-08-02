<?php

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case CAJA = 'CAJA';
    case MOZO = 'MOZO';
    case COCINA = 'COCINA';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::CAJA => 'Caja',
            self::MOZO => 'Mozo',
            self::COCINA => 'Cocina',
        };
    }
}
