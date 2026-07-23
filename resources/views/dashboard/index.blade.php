<x-layouts.app title="Panel principal">
    <div class="card" style="margin-bottom:18px;">
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
            <div>
                <strong>Resumen general</strong>
                <div class="muted">Acceso rapido a las areas principales y reportes.</div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a class="btn primary" href="{{ route('reports.daily-sales') }}">Ventas del dia</a>
                <a class="btn" href="{{ route('reports.daily') }}">Resumen del dia</a>
                <a class="btn" href="{{ route('reports.monthly') }}">Resumen del mes</a>
                <a class="btn" href="{{ route('reports.sold-products', ['period' => 'today']) }}">Productos vendidos</a>
            </div>
        </div>
    </div>

    <div class="grid metrics">
        <a class="card metric metric-link" href="{{ route('products.index') }}">
            <div class="metric-icon">P</div>
            <div>
                <div>Productos</div>
                <div class="metric-value">{{ $dashboard['totalProducts'] }}</div>
                <div class="muted">Ver y administrar productos</div>
            </div>
        </a>
        <a class="card metric metric-link" href="{{ route('tables.index') }}">
            <div class="metric-icon green">M</div>
            <div>
                <div>Mesas libres</div>
                <div class="metric-value">{{ $dashboard['freeTables'] }}</div>
                <div class="muted">De {{ $dashboard['totalTables'] }} mesas totales</div>
            </div>
        </a>
        <a class="card metric metric-link" href="{{ route('tables.index') }}">
            <div class="metric-icon red">M</div>
            <div>
                <div>Mesas ocupadas</div>
                <div class="metric-value">{{ $dashboard['occupiedTables'] }}</div>
                <div class="muted">{{ $dashboard['occupancyPercentage'] }}% de ocupacion</div>
            </div>
        </a>
        <a class="card metric metric-link" href="{{ route('tables.index') }}">
            <div class="metric-icon">O</div>
            <div>
                <div>Pedidos abiertos</div>
                <div class="metric-value">{{ $dashboard['openOrders'] }}</div>
                <div class="muted">Mesas con consumo activo</div>
            </div>
        </a>
    </div>

    <div class="grid metrics" style="margin-top:18px;">
        <a class="card metric metric-link" href="{{ route('reports.daily-sales') }}">
            <div class="metric-icon green">$</div>
            <div>
                <div>Ventas del dia</div>
                <div class="metric-value">${{ number_format($dashboard['salesTodayInCents'] / 100, 0, ',', '.') }}</div>
                <div class="muted">{{ $dashboard['dailySales']['closedOrdersCount'] }} pedidos cerrados</div>
            </div>
        </a>
        <a class="card metric metric-link" href="{{ route('reports.monthly') }}">
            <div class="metric-icon green">$</div>
            <div>
                <div>Ventas del mes</div>
                <div class="metric-value">${{ number_format($dashboard['salesMonthInCents'] / 100, 0, ',', '.') }}</div>
                <div class="muted">Ver resumen mensual</div>
            </div>
        </a>
        <a class="card metric metric-link" href="{{ route('reports.sold-products', ['period' => 'today']) }}">
            <div class="metric-icon">P</div>
            <div>
                <div>Productos vendidos hoy</div>
                <div class="metric-value">{{ $dashboard['productsSoldToday'] }}</div>
                <div class="muted">Ranking y totales por producto</div>
            </div>
        </a>
        <a class="card metric metric-link" href="{{ route('tables.index') }}">
            <div class="metric-icon red">M</div>
            <div>
                <div>Ocupacion</div>
                <div class="metric-value">{{ $dashboard['occupiedTables'] }}/{{ $dashboard['totalTables'] }}</div>
                <div class="muted">{{ $dashboard['occupancyPercentage'] }}% de mesas en uso</div>
            </div>
        </a>
    </div>

    <div class="grid two-columns" style="margin-top:18px;">
        <div class="card">
            <div class="card-header">
                <strong>Resumen de ventas de hoy</strong>
                <a class="btn" href="{{ route('reports.daily-sales') }}">Ver detalle</a>
            </div>
            <div class="card-body summary-list">
                <div><span>Total vendido</span><strong>${{ number_format($dashboard['dailySales']['totalInCents'] / 100, 0, ',', '.') }}</strong></div>
                <div><span>Efectivo</span><strong>${{ number_format($dashboard['dailySales']['cashInCents'] / 100, 0, ',', '.') }}</strong></div>
                <div><span>Transferencia</span><strong>${{ number_format($dashboard['dailySales']['transferInCents'] / 100, 0, ',', '.') }}</strong></div>
                <div><span>Tarjeta</span><strong>${{ number_format($dashboard['dailySales']['cardInCents'] / 100, 0, ',', '.') }}</strong></div>
                <div><span>Promedio por ticket</span><strong>${{ number_format($dashboard['dailySales']['averageTicketInCents'] / 100, 0, ',', '.') }}</strong></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Resumen operativo</strong>
                <a class="btn" href="{{ route('tables.index') }}">Ver mesas</a>
            </div>
            <div class="card-body summary-list">
                <div><span>Mesas totales</span><strong>{{ $dashboard['totalTables'] }}</strong></div>
                <div><span>Mesas libres</span><strong>{{ $dashboard['freeTables'] }}</strong></div>
                <div><span>Mesas ocupadas</span><strong>{{ $dashboard['occupiedTables'] }}</strong></div>
                <div><span>Mesas cerradas hoy</span><strong>{{ $dashboard['closedTableStats']['today'] }}</strong></div>
                <div><span>Promedio jue-dom</span><strong>{{ number_format($dashboard['closedTableStats']['weekendAverage'], 1, ',', '.') }}</strong></div>
                <div><span>Promedio mensual</span><strong>{{ number_format($dashboard['closedTableStats']['monthlyAverage'], 1, ',', '.') }}</strong></div>
            </div>
        </div>
    </div>

    <div class="grid two-columns tables-overview">
        <div class="card">
            <div class="card-header">
                <strong>Gestion de Mesas</strong>
                <a class="btn" href="{{ route('tables.index') }}">Ver todas</a>
            </div>
            <div class="card-body">
                <div class="legend">
                    <span><span class="dot free"></span>Libre</span>
                    <span><span class="dot occupied"></span>Ocupada</span>
                </div>
                <div class="table-grid">
                    @foreach ($dashboard['tables'] as $table)
                        <a class="table-cell {{ $table->isOccupied() ? 'occupied' : '' }}" href="{{ route('tables.show', $table->number()) }}">
                            {{ $table->number() }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Mesas cerradas</strong>
                <a class="btn" href="{{ route('reports.daily-sales') }}">Ver ventas</a>
            </div>
            <div class="card-body" style="padding:0;">
                <table>
                    <thead>
                        <tr>
                            <th>Mesa</th>
                            <th>Total</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dashboard['recentClosedTables'] as $closedTable)
                            <tr>
                                <td>Mesa {{ $closedTable['tableNumber'] }}</td>
                                <td>${{ number_format($closedTable['totalInCents'] / 100, 0, ',', '.') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('tickets.reprint', $closedTable['orderId']) }}">
                                        @csrf
                                        <button class="btn" type="submit">Reimprimir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="muted">No hay mesas cerradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
