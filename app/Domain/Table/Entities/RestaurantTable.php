<?php

namespace App\Domain\Table\Entities;

final readonly class RestaurantTable
{
    public function __construct(
        private ?int $id,
        private int $number,
        private ?Order $openOrder,
    ) {
        if ($number < 1 || $number > 50) {
            throw new \InvalidArgumentException('El numero de mesa debe estar entre 1 y 50.');
        }
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function openOrder(): ?Order
    {
        return $this->openOrder;
    }

    public function isOccupied(): bool
    {
        return $this->openOrder !== null
            && $this->openOrder->isOpen()
            && count($this->openOrder->items()) > 0;
    }
}
