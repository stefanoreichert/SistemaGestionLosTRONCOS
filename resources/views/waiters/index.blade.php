<x-layouts.app title="Mozos">
    <div data-theme="emerald" class="card bg-base-100 border border-base-300 shadow-sm">
        <div class="card-header">
            <strong>Mozos</strong>
            <a class="btn btn-primary primary" href="{{ route('waiters.create') }}">Crear mozo</a>
        </div>
        <div class="card-body">
            <form class="flex flex-wrap gap-[10px]" method="GET" action="{{ route('waiters.index') }}">
                <input
                    class="input input-bordered flex-1 min-w-[220px]"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Buscar por nombre, código o teléfono"
                    aria-label="Buscar mozos"
                >
                <button class="btn btn-primary primary" type="submit">Buscar</button>
                @if ($search !== '')
                    <a class="btn btn-outline" href="{{ route('waiters.index') }}">Limpiar</a>
                @endif
            </form>
        </div>
        <div class="card-body overflow-x-auto !p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Teléfono</th>
                        <th>Disponibilidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($waiters as $waiter)
                        <tr @class(['bg-base-200/50 opacity-80' => ! $waiter->isActive()])>
                            <td><strong>{{ $waiter->name() }}</strong></td>
                            <td>{{ $waiter->employeeCode() ?? 'Sin código' }}</td>
                            <td>{{ $waiter->phone() ?? 'Sin teléfono' }}</td>
                            <td>
                                <form
                                    class="flex items-center gap-3 whitespace-nowrap"
                                    method="POST"
                                    action="{{ route('waiters.availability', $waiter->id()) }}"
                                    data-waiter-availability-form
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $waiter->isActive() ? '0' : '1' }}">
                                    <input
                                        type="checkbox"
                                        class="toggle toggle-success"
                                        @checked($waiter->isActive())
                                        aria-label="{{ $waiter->isActive() ? 'Desactivar mozo' : 'Activar mozo' }}"
                                        data-waiter-availability-toggle
                                    >
                                    <span class="badge {{ $waiter->isActive() ? 'badge-success' : 'badge-error' }}">
                                        {{ $waiter->isActive() ? 'Activo' : 'Inactivo' }}
                                    </span>
                                    <span
                                        class="loading loading-spinner loading-sm hidden"
                                        aria-label="Procesando"
                                        data-waiter-availability-spinner
                                    ></span>
                                </form>
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-outline" href="{{ route('waiters.edit', $waiter->id()) }}">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">
                                {{ $search !== '' ? 'No se encontraron mozos.' : 'No hay mozos cargados.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
