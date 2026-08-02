<x-layouts.app title="Crear usuario">
    <div data-theme="emerald" class="card mx-auto max-w-3xl border border-base-300 bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Crear usuario</h2>
            <p class="text-sm text-base-content/70">Las cuentas MOZO reciben automáticamente un perfil operativo exclusivo.</p>
            <form class="space-y-4" method="POST" action="{{ route('users.store') }}">
                @csrf
                @include('users._form')
            </form>
        </div>
    </div>
</x-layouts.app>
