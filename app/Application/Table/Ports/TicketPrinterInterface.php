<?php

namespace App\Application\Table\Ports;

use App\Domain\Table\Entities\Order;

interface TicketPrinterInterface
{
    public function print(Order $order): void;


}
