<?php

namespace App\Application\Tickets\UseCases;

use App\Application\Tickets\DTOs\TicketFiltersDTO;
use App\Domain\Table\Entities\Order;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;

final readonly class ListClosedTicketsUseCase
{
    public function __construct(private TicketRepositoryInterface $tickets)
    {
    }

    /**
     * @return list<Order>
     */
    public function execute(TicketFiltersDTO $filters): array
    {
        return $this->tickets->listClosed($filters);
    }
}
