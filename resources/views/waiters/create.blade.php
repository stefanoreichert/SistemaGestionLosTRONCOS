<x-layouts.app title="Crear mozo">
    <div data-theme="emerald" class="card bg-base-100 border border-base-300 shadow-sm">
        <div class="card-header"><strong>Nuevo mozo</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('waiters.store') }}">
                @csrf
                @include('waiters._form')
            </form>
        </div>
    </div>
</x-layouts.app>
