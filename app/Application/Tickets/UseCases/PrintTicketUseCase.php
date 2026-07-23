<?php

namespace App\Application\Tickets\UseCases;

use App\Application\Table\Ports\TicketPrinterInterface;
use App\Domain\Table\Entities\Order;

final readonly class PrintTicketUseCase
{
    public function __construct(private TicketPrinterInterface $ticketPrinter)
    {
    }

    public function execute(Order $order): Order
    {
        $this->ticketPrinter->print($order);

        return $order;
    }
}
