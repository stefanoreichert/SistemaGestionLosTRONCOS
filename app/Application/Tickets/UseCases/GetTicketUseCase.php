<?php

namespace App\Application\Tickets\UseCases;

use App\Domain\Table\Entities\Order;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;

final readonly class GetTicketUseCase
{
    public function __construct(private TicketRepositoryInterface $tickets)
    {
    }

    public function execute(int $orderId): ?Order
    {
        return $this->tickets->findClosedById($orderId);
    }
}
