<?php

namespace App\Application\Table\UseCases;

use App\Application\Table\DTOs\UpdateProductQuantityDTO;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class UpdateProductQuantityUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    public function execute(UpdateProductQuantityDTO $dto): Order
    {
        return $this->orders->updateProductQuantity($dto->tableNumber, $dto->productId, $dto->quantity);
    }
}
