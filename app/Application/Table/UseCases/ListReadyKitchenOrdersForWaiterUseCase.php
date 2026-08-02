<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class ListReadyKitchenOrdersForWaiterUseCase
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function execute(int $waiterId, string $since): array
    {
        return $this->orders->readyForWaiterSince($waiterId, $since);
    }
}
