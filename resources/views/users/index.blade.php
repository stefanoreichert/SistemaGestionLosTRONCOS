<x-layouts.app title="Usuarios">
    <div data-theme="emerald" class="card border border-base-300 bg-base-100 shadow-sm">
        <div class="card-body gap-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="card-title">Usuarios</h2>
                    <p class="text-sm text-base-content/70">Administración de cuentas y accesos del sistema.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('users.create') }}">Crear usuario</a>
            </div>
            <form class="flex flex-wrap gap-3" method="GET" action="{{ route('users.index') }}">
                <input class="input input-bordered min-w-[220px] flex-1" type="search" name="search" value="{{ $search }}" placeholder="Buscar por nombre, email o teléfono" aria-label="Buscar usuarios">
                <button class="btn btn-primary" type="submit">Buscar</button>
                @if ($search !== '')
                    <a class="btn btn-outline" href="{{ route('users.index') }}">Limpiar</a>
                @endif
            </form>
            @error('is_active') <div class="alert alert-error" role="alert">{{ $message }}</div> @enderror
            @error('role') <div class="alert alert-error" role="alert">{{ $message }}</div> @enderror
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Rol</th><th>Estado</th><th>Mozo relacionado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $account)
                        <tr @class(['opacity-70' => ! $account->is_active])>
                            <td><strong>{{ $account->name }}</strong></td>
                            <td>{{ $account->email }}</td>
                            <td>{{ $account->phone ?? '—' }}</td>
                            <td><span class="badge badge-outline">{{ $account->role }}</span></td>
                            <td>
                                <form class="flex items-center gap-3 whitespace-nowrap" method="POST" action="{{ route('users.availability', $account) }}" data-user-availability-form>
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $account->is_active ? '0' : '1' }}">
                                    <input type="checkbox" class="toggle toggle-success" @checked($account->is_active) @disabled(auth()->id() === $account->id) aria-label="{{ $account->is_active ? 'Desactivar usuario' : 'Activar usuario' }}" data-user-availability-toggle>
                                    <span class="badge {{ $account->is_active ? 'badge-success' : 'badge-error' }}">{{ $account->is_active ? 'Activo' : 'Inactivo' }}</span>
                                    <span class="loading loading-spinner loading-sm hidden" aria-label="Procesando" data-user-availability-spinner></span>
                                </form>
                            </td>
                            <td>{{ $account->waiter?->name ?? '—' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn btn-outline btn-sm" href="{{ route('users.edit', $account) }}">Editar</a>
                                    <a class="btn btn-warning btn-sm" href="{{ route('users.edit', $account) }}#password">Cambiar contraseña</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">{{ $search !== '' ? 'No se encontraron usuarios.' : 'No hay usuarios cargados.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-body">{{ $users->links() }}</div>
        @endif
    </div>
</x-layouts.app>
