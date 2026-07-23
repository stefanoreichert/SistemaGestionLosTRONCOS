<x-layouts.app title="Ventas del día">
    @php
        $paymentLabels = [
            'cash' => 'Efectivo',
            'transfer' => 'Transferencia',
            'card' => 'Tarjeta',
        ];
    @endphp

    <div class="grid metrics">
        <div class="card metric"><div><div>Total vendido</div><div class="metric-value">${{ number_format($report['totalInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card metric"><div><div>Efectivo</div><div class="metric-value">${{ number_format($report['cashInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card metric"><div><div>Transferencia</div><div class="metric-value">${{ number_format($report['transferInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card metric"><div><div>Tarjeta</div><div class="metric-value">${{ number_format($report['cardInCents'] / 100, 0, ',', '.') }}</div></div></div>
    </div>

    <div class="grid metrics" style="margin-top:18px;">
        <div class="card metric"><div><div>Mesas cerradas</div><div class="metric-value">{{ $report['closedTablesCount'] }}</div></div></div>
        <div class="card metric"><div><div>Pedidos cerrados</div><div class="metric-value">{{ $report['closedOrdersCount'] }}</div></div></div>
        <div class="card metric"><div><div>Promedio por ticket</div><div class="metric-value">${{ number_format($report['averageTicketInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card metric"><div><div>Fecha</div><div class="metric-value" style="font-size:22px;">{{ $report['date'] }}</div></div></div>
    </div>

    <div class="card" style="margin-top:18px;">
        <div class="card-header"><strong>Pedidos cerrados del día</strong></div>
        <div class="card-body" style="padding:0;overflow:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Mesa</th>
                        <th>Método de pago</th>
                        <th>Total</th>
                        <th>Hora de cierre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['orders'] as $order)
                        <tr>
                            <td>Mesa {{ $order['tableNumber'] }}</td>
                            <td>{{ $paymentLabels[$order['paymentMethod']] ?? 'Sin metodo' }}</td>
                            <td>${{ number_format($order['totalInCents'] / 100, 0, ',', '.') }}</td>
                            <td>{{ $order['closedAt'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">No hay pedidos cerrados hoy.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
