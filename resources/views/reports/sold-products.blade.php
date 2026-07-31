<x-layouts.app title="Productos Vendidos">
    <form class="card bg-base-100 border border-base-300 shadow-sm mb-[18px]" method="GET" action="{{ route('reports.sold-products') }}">
        <div class="card-body">
            <div class="actions mb-4 flex-wrap">
                <a class="btn btn-outline {{ $period === 'today' ? 'btn-primary primary' : '' }}" href="{{ route('reports.sold-products', ['period' => 'today']) }}">Hoy</a>
                <a class="btn btn-outline {{ $period === 'month' ? 'btn-primary primary' : '' }}" href="{{ route('reports.sold-products', ['period' => 'month']) }}">Este mes</a>
            </div>

            <input type="hidden" name="period" value="custom">
            <div class="form-grid">
                <div>
                    <label for="from">Desde</label>
                    <input class="input input-bordered" id="from" name="from" type="date" value="{{ $report['from'] }}">
                    @error('from') <div class="error text-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="to">Hasta</label>
                    <input class="input input-bordered" id="to" name="to" type="date" value="{{ $report['to'] }}">
                    @error('to') <div class="error text-error">{{ $message }}</div> @enderror
                </div>
                <div class="field full">
                    <button class="btn btn-primary primary" type="submit">Filtrar</button>
                </div>
            </div>
        </div>
    </form>

    <div class="grid metrics">
        <div class="card bg-base-100 border border-base-300 shadow-sm metric">
            <div>
                <div>Total productos vendidos</div>
                <div class="metric-value">{{ $report['totalQuantity'] }}</div>
                <div class="muted">{{ $report['from'] }} al {{ $report['to'] }}</div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-300 shadow-sm metric">
            <div>
                <div>Total recaudado</div>
                <div class="metric-value">${{ number_format($report['totalInCents'] / 100, 0, ',', '.') }}</div>
                <div class="muted">Solo pedidos cerrados</div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300 shadow-sm mt-[18px]">
        <div class="card-header"><strong>Productos vendidos</strong></div>
        <div class="card-body !p-0 overflow-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoria</th>
                        <th>Cantidad vendida</th>
                        <th>Total recaudado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['products'] as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['category'] }}</td>
                            <td>{{ $product['quantity'] }}</td>
                            <td>${{ number_format($product['totalInCents'] / 100, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">No hay productos vendidos en este periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
