<x-layouts.app title="Historial de tickets">
    @php
        $paymentLabels = [
            'cash' => 'Efectivo',
            'transfer' => 'Transferencia',
            'card' => 'Tarjeta',
        ];
    @endphp

    <form class="card" method="GET" action="{{ route('tickets.index') }}" style="margin-bottom:18px;">
        <div class="card-body form-grid">
            <div>
                <label for="from">Desde</label>
                <input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}">
            </div>
            <div>
                <label for="to">Hasta</label>
                <input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}">
            </div>
            <div>
                <label for="table_number">Mesa</label>
                <input id="table_number" name="table_number" type="number" min="1" value="{{ $filters['table_number'] ?? '' }}">
            </div>
            <div>
                <label for="payment_method">Metodo de pago</label>
                <select id="payment_method" name="payment_method">
                    <option value="">Todos</option>
                    @foreach ($paymentLabels as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['payment_method'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field full" style="display:flex;gap:10px;flex-wrap:wrap;">
                <button class="btn primary" type="submit">Filtrar</button>
                <a class="btn" href="{{ route('tickets.index') }}">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header"><strong>Tickets cerrados</strong></div>
        <div class="card-body" style="padding:0;overflow:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Mesa</th>
                        <th>Cierre</th>
                        <th>Metodo de pago</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticketNumber() ?? 'Sin numero' }}</td>
                            <td>Mesa {{ $ticket->tableNumber() }}</td>
                            <td>{{ $ticket->closedAt() }}</td>
                            <td>{{ $paymentLabels[$ticket->paymentMethod()] ?? 'Sin dato' }}</td>
                            <td>${{ number_format($ticket->totalInCents() / 100, 0, ',', '.') }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn" href="{{ route('tickets.show', $ticket->id()) }}">Ver ticket</a>
                                    <form method="POST" action="{{ route('tickets.reprint', $ticket->id()) }}">
                                        @csrf
                                        <button class="btn primary" type="submit">Reimprimir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">No hay tickets cerrados para estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
