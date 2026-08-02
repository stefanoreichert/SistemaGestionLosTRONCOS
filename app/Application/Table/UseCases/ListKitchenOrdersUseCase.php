<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class ListKitchenOrdersUseCase
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function execute(): array
    {
        return $this->orders->activeKitchenOrders();
    }
}
