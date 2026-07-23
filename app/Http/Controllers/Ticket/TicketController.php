<?php

namespace App\Http\Controllers\Ticket;

use App\Application\Tickets\DTOs\TicketFiltersDTO;
use App\Application\Tickets\UseCases\GetTicketUseCase;
use App\Application\Tickets\UseCases\ListClosedTicketsUseCase;
use App\Application\Tickets\UseCases\ReprintTicketUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ListTicketsRequest;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(ListTicketsRequest $request, ListClosedTicketsUseCase $useCase): View
    {
        $validated = $request->validated();

        return view('tickets.index', [
            'filters' => $validated,
            'tickets' => $useCase->execute(new TicketFiltersDTO(
                from: $validated['from'] ?? null,
                to: $validated['to'] ?? null,
                tableNumber: isset($validated['table_number']) ? (int) $validated['table_number'] : null,
                paymentMethod: $validated['payment_method'] ?? null,
            )),
        ]);
    }

    public function show(int $order, GetTicketUseCase $useCase): View
    {
        $ticket = $useCase->execute($order);

        abort_if($ticket === null, 404);

        return view('tables.ticket', [
            'order' => $ticket,
            'backUrl' => route('tickets.index'),
        ]);
    }

    public function reprint(int $order, ReprintTicketUseCase $useCase): View
    {
        $ticket = $useCase->execute($order);

        abort_if($ticket === null, 404);

        return view('tables.ticket', [
            'order' => $ticket,
            'backUrl' => route('tickets.index'),
        ]);
    }
}
