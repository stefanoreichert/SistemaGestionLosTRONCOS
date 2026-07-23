<x-layouts.app title="Mesas">
    <div class="card">
        <div class="card-header"><strong>Gestion de Mesas</strong></div>
        <div class="card-body">
            <div class="legend">
                <span><span class="dot free"></span>Libre</span>
                <span><span class="dot occupied"></span>Ocupada</span>
            </div>
            <div class="table-grid">
                @foreach ($tables as $table)
                    <a class="table-cell {{ $table->isOccupied() ? 'occupied' : '' }}" href="{{ route('tables.show', $table->number()) }}">
                        {{ $table->number() }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
