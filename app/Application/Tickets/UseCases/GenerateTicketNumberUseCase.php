<?php

namespace App\Application\Tickets\UseCases;

use App\Domain\Table\Entities\Order;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;

final readonly class GenerateTicketNumberUseCase
{
    public function __construct(private TicketRepositoryInterface $tickets)
    {
    }

    public function execute(Order $order): Order
    {
        if ($order->ticketNumber() !== null || $order->id() === null) {
            return $order;
        }

        return $this->tickets->assignNextTicketNumber($order->id());
    }
}
