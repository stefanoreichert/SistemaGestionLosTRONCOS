<?php

namespace App\Domain\Table\Entities;

final readonly class Order
{
    /**
     * @param list<OrderItem> $items
     */
    public function __construct(
        private ?int $id,
        private int $tableId,
        private int $tableNumber,
        private string $status,
        private int $subtotalInCents,
        private int $totalInCents,
        private ?string $paymentMethod,
        private ?string $ticketNumber,
        private string $openedAt,
        private ?string $closedAt,
        private array $items,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function tableId(): int
    {
        return $this->tableId;
    }

    public function tableNumber(): int
    {
        return $this->tableNumber;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function subtotalInCents(): int
    {
        return $this->subtotalInCents;
    }

    public function totalInCents(): int
    {
        return $this->totalInCents;
    }

    public function paymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function ticketNumber(): ?string
    {
        return $this->ticketNumber;
    }

    public function openedAt(): string
    {
        return $this->openedAt;
    }

    public function closedAt(): ?string
    {
        return $this->closedAt;
    }

    /**
     * @return list<OrderItem>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
