<?php

namespace App\Application\Tickets\UseCases;

use App\Domain\Table\Entities\Order;

final readonly class ReprintTicketUseCase
{
    public function __construct(
        private GetTicketUseCase $getTicket,
        private PrintTicketUseCase $printTicket,
    ) {
    }

    public function execute(int $orderId): ?Order
    {
        $order = $this->getTicket->execute($orderId);

        return $order !== null ? $this->printTicket->execute($order) : null;
    }
}
