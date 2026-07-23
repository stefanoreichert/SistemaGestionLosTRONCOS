<?php

namespace App\Domain\Tickets\Repositories;

use App\Application\Tickets\DTOs\TicketFiltersDTO;
use App\Domain\Table\Entities\Order;

interface TicketRepositoryInterface
{
    public function assignNextTicketNumber(int $orderId): Order;

    public function findClosedById(int $orderId): ?Order;

    /**
     * @return list<Order>
     */
    public function listClosed(TicketFiltersDTO $filters): array;
}
