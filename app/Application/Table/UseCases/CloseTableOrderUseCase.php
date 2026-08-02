<?php

namespace App\Application\Table\UseCases;

use App\Application\Table\DTOs\AuthenticatedOrderOperatorDTO;
use App\Application\Table\DTOs\CloseTableOrderDTO;
use App\Application\Tickets\UseCases\GenerateTicketNumberUseCase;
use App\Application\Tickets\UseCases\PrintTicketUseCase;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class CloseTableOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private GenerateTicketNumberUseCase $generateTicketNumber,
        private PrintTicketUseCase $printTicket,
    ) {}

    public function execute(CloseTableOrderDTO $dto, AuthenticatedOrderOperatorDTO $operator): Order
    {
        $order = $this->orders->closeByTableNumber(
            $dto->tableNumber,
            $dto->paymentMethod,
            $operator->isAdmin,
            $operator->waiterId,
        );
        $order = $this->generateTicketNumber->execute($order);

        $this->printTicket->execute($order);

        return $order;
    }
}
