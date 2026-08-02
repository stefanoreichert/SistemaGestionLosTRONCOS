<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class UpdateKitchenStatusUseCase
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function execute(int $orderId, string $nextStatus): Order
    {
        return $this->orders->updateKitchenStatus($orderId, $nextStatus);
    }
}
