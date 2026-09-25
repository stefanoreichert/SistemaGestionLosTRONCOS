<x-layouts.app title="Resumen Diario">
    <div class="daily-report-controls">
    <form class="card bg-base-100 border border-base-300 shadow-sm mb-[18px]" method="GET" action="{{ route('reports.daily') }}">
        <div class="card-body">
            <label for="date">Fecha</label>
            <div class="flex gap-[10px]">
                <input class="input input-bordered" id="date" name="date" type="date" value="{{ $report['date'] }}">
                <button class="btn btn-primary primary" type="submit">Filtrar</button>
            </div>
        </div>
    </form>

        @if ($report['isCurrentPeriod'])
            <form method="POST" action="{{ route('reports.daily-closures.store') }}" data-daily-report-closure-form>
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $closureToken }}">
                <button class="btn btn-error" type="submit" @disabled($report['ordersCount'] === 0)>
                    Imprimir y cerrar día
                </button>
            </form>
        @endif
    </div>

    @include('reports.partials.daily-summary', ['report' => $report])
</x-layouts.app>
