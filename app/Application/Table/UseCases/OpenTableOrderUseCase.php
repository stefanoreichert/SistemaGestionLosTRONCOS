<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class OpenTableOrderUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    public function execute(int $tableNumber): Order
    {
        return $this->orders->openForTableNumber($tableNumber);
    }
}
