<x-layouts.app title="Resumen Mensual">
    <form class="card bg-base-100 border border-base-300 shadow-sm mb-[18px]" method="GET" action="{{ route('reports.monthly') }}">
        <div class="card-body form-grid">
            <div>
                <label for="month">Mes</label>
                <input class="input input-bordered" id="month" name="month" type="number" min="1" max="12" value="{{ $report['month'] }}">
            </div>
            <div>
                <label for="year">Año</label>
                <input class="input input-bordered" id="year" name="year" type="number" min="2020" max="2100" value="{{ $report['year'] }}">
            </div>
            <div class="field full"><button class="btn btn-primary primary" type="submit">Filtrar</button></div>
        </div>
    </form>

    <div class="grid metrics">
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Facturacion</div><div class="metric-value">${{ number_format($report['billingInCents'] / 100, 0, ',', '.') }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Pedidos</div><div class="metric-value">{{ $report['ordersCount'] }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Productos vendidos</div><div class="metric-value">{{ $report['soldProductsCount'] }}</div></div></div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric"><div><div>Promedio diario</div><div class="metric-value">${{ number_format($report['dailyAverageInCents'] / 100, 0, ',', '.') }}</div></div></div>
    </div>

    <div class="grid two-columns">
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-header"><strong>Indicadores</strong></div>
            <div class="card-body">
                <p><strong>Producto mas vendido:</strong> {{ $report['topProduct'] ?? 'Sin datos' }}</p>
                <p><strong>Categoria mas vendida:</strong> {{ $report['topCategory'] ?? 'Sin datos' }}</p>
                <p><strong>Promedio por ticket:</strong> ${{ number_format($report['averagePerTicketInCents'] / 100, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-header"><strong>Ventas por dia</strong></div>
            <div class="card-body">
                @forelse ($report['salesByDay'] as $row)
                    <p>{{ $row['label'] }} - ${{ number_format($row['totalInCents'] / 100, 0, ',', '.') }}</p>
                @empty
                    <p class="muted">Sin ventas.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300 shadow-sm mt-[18px]">
        <div class="card-header"><strong>Ranking de productos</strong></div>
        <div class="card-body overflow-x-auto !p-0">
            <table class="table">
                <thead><tr><th>Producto</th><th>Categoria</th><th>Cantidad</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($report['productRanking'] as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['category'] }}</td>
                            <td>{{ $row['sold'] }}</td>
                            <td>${{ number_format($row['totalInCents'] / 100, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">Sin productos vendidos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
