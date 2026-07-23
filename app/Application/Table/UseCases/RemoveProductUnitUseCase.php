<?php

namespace App\Application\Table\UseCases;

use App\Application\Table\DTOs\RemoveProductFromOrderDTO;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class RemoveProductUnitUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    public function execute(RemoveProductFromOrderDTO $dto): Order
    {
        return $this->orders->removeProductUnit($dto->tableNumber, $dto->productId);
    }
}
