<div class="daily-report-summary">
    <div class="grid metrics">
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Total vendido</div><div class="metric-value">${{ number_format($report['totalSoldInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Pedidos</div><div class="metric-value">{{ $report['ordersCount'] }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Mesas utilizadas</div><div class="metric-value">{{ $report['usedTablesCount'] }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Productos vendidos</div><div class="metric-value">{{ $report['soldProductsCount'] }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Promedio ticket</div><div class="metric-value">${{ number_format($report['averagePerTicketInCents'] / 100, 0, ',', '.') }}</div></div></div>
    </div>

    <div class="grid two-columns">
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-header"><strong>Indicadores</strong></div>
            <div class="card-body">
                <p><strong>Producto más vendido:</strong> {{ $report['topProduct'] ?? 'Sin datos' }}</p>
                <p><strong>Categoría más vendida:</strong> {{ $report['topCategory'] ?? 'Sin datos' }}</p>
                <p><strong>Promedio por mesa:</strong> ${{ number_format($report['averagePerTableInCents'] / 100, 0, ',', '.') }}</p>
                <p><strong>Efectivo:</strong> ${{ number_format($report['cashTotalInCents'] / 100, 0, ',', '.') }}</p>
                <p><strong>Transferencia:</strong> ${{ number_format($report['transferTotalInCents'] / 100, 0, ',', '.') }}</p>
                <p><strong>Tarjeta:</strong> ${{ number_format($report['cardTotalInCents'] / 100, 0, ',', '.') }}</p>
                <p><strong>Total general:</strong> ${{ number_format($report['grandTotalInCents'] / 100, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-300 shadow-sm">
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

    <div class="card bg-base-100 border border-base-300 shadow-sm mt-[18px]">
        <div class="card-header"><strong>Pedidos cerrados</strong></div>
        <div class="card-body overflow-x-auto !p-0">
            <table class="table">
                <thead><tr><th>Mesa</th><th>Fecha</th><th>Ítems</th><th>Método de pago</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($report['orders'] as $order)
                        <tr>
                            <td>Mesa {{ $order['tableNumber'] }}</td>
                            <td>{{ $order['closedAt'] }}</td>
                            <td>{{ $order['itemsCount'] }}</td>
                            <td>{{ $order['paymentMethodLabel'] }}</td>
                            <td>${{ number_format($order['totalInCents'] / 100, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">No hay pedidos cerrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
