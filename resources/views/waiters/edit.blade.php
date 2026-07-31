<x-layouts.app title="Editar mozo">
    <div data-theme="emerald" class="card bg-base-100 border border-base-300 shadow-sm">
        <div class="card-header"><strong>Editar mozo</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('waiters.update', $waiter->id()) }}">
                @csrf
                @method('PUT')
                @include('waiters._form', ['waiter' => $waiter])
            </form>
        </div>
    </div>
</x-layouts.app>
