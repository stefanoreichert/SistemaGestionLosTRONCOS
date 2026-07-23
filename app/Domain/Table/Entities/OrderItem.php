<?php

namespace App\Domain\Table\Entities;

final readonly class OrderItem
{
    public function __construct(
        private ?int $id,
        private int $productId,
        private string $productName,
        private string $category,
        private int $quantity,
        private int $unitPriceInCents,
        private int $subtotalInCents,
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function productId(): int
    {
        return $this->productId;
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPriceInCents(): int
    {
        return $this->unitPriceInCents;
    }

    public function subtotalInCents(): int
    {
        return $this->subtotalInCents;
    }
}
