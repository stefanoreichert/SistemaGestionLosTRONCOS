<x-layouts.app title="Resumen Diario">
    <form class="card" method="GET" action="{{ route('reports.daily') }}" style="margin-bottom:18px;">
        <div class="card-body">
            <label for="date">Fecha</label>
            <div style="display:flex;gap:10px;">
                <input id="date" name="date" type="date" value="{{ $report['date'] }}">
                <button class="btn primary" type="submit">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="grid metrics">
        <div class="card metric"><div><div>Total vendido</div><div class="metric-value">${{ number_format($report['totalSoldInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card metric"><div><div>Pedidos</div><div class="metric-value">{{ $report['ordersCount'] }}</div></div></div>
        <div class="card metric"><div><div>Mesas utilizadas</div><div class="metric-value">{{ $report['usedTablesCount'] }}</div></div></div>
        <div class="card metric"><div><div>Promedio ticket</div><div class="metric-value">${{ number_format($report['averagePerTicketInCents'] / 100, 0, ',', '.') }}</div></div></div>
    </div>

    <div class="grid two-columns">
        <div class="card">
            <div class="card-header"><strong>Indicadores</strong></div>
            <div class="card-body">
                <p><strong>Producto mas vendido:</strong> {{ $report['topProduct'] ?? 'Sin datos' }}</p>
                <p><strong>Categoria mas vendida:</strong> {{ $report['topCategory'] ?? 'Sin datos' }}</p>
                <p><strong>Promedio por mesa:</strong> ${{ number_format($report['averagePerTableInCents'] / 100, 0, ',', '.') }}</p>
                <p><strong>Total efectivo:</strong> ${{ number_format($report['cashTotalInCents'] / 100, 0, ',', '.') }}</p>
                <p><strong>Total general:</strong> ${{ number_format($report['grandTotalInCents'] / 100, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><strong>Ventas por hora</strong></div>
            <div class="card-body">
                @forelse ($report['salesByHour'] as $row)
                    <p>{{ $row['label'] }} - ${{ number_format($row['totalInCents'] / 100, 0, ',', '.') }}</p>
                @empty
                    <p class="muted">Sin ventas.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:18px;">
        <div class="card-header"><strong>Pedidos cerrados</strong></div>
        <div class="card-body" style="padding:0;">
            <table>
                <thead><tr><th>Mesa</th><th>Fecha</th><th>Items</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($report['orders'] as $order)
                        <tr>
                            <td>Mesa {{ $order->tableNumber() }}</td>
                            <td>{{ $order->closedAt() }}</td>
                            <td>{{ count($order->items()) }}</td>
                            <td>${{ number_format($order->totalInCents() / 100, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">No hay pedidos cerrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
