<?php

namespace App\Application\Table\UseCases;

use App\Application\Table\DTOs\AddProductToOrderDTO;
use App\Application\Table\DTOs\AuthenticatedOrderOperatorDTO;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class AddProductToOrderUseCase
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function execute(AddProductToOrderDTO $dto, AuthenticatedOrderOperatorDTO $operator): Order
    {
        return $this->orders->addProduct(
            $dto->tableNumber,
            $dto->productId,
            $operator->isAdmin,
            $operator->waiterId,
        );
    }
}
