<?php

namespace App\Application\Table\UseCases;

use App\Application\Table\DTOs\AddProductToOrderDTO;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class AddProductToOrderUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    public function execute(AddProductToOrderDTO $dto): Order
    {
        return $this->orders->addProduct($dto->tableNumber, $dto->productId);
    }
}
