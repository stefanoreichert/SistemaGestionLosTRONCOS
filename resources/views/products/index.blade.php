<x-layouts.app title="Productos">
    <div class="card bg-base-100 border border-base-300 shadow-sm">
        <div class="card-header">
            <strong>Productos</strong>
            <a class="btn btn-primary primary" href="{{ route('products.create') }}">Crear producto</a>
        </div>
        <div class="card-body">
            <form class="flex flex-wrap gap-[10px]" method="GET" action="{{ route('products.index') }}">
                <input
                    class="input input-bordered flex-1 min-w-[220px]"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Buscar por nombre o categoría"
                    aria-label="Buscar productos"
                >
                <button class="btn btn-primary primary" type="submit">Buscar</button>
                @if ($search !== '')
                    <a class="btn btn-outline" href="{{ route('products.index') }}">Limpiar</a>
                @endif
            </form>
        </div>
        <div class="card-body overflow-x-auto !p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Disponibilidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr @class(['bg-base-200/50 opacity-80' => ! $product->isActive()])>
                            <td>
                                <strong>{{ $product->name() }}</strong>
                            </td>
                            <td>{{ $product->category() }}</td>
                            <td>${{ number_format($product->priceInCents() / 100, 0, ',', '.') }}</td>
                            <td>
                                <form
                                    class="flex items-center gap-3 whitespace-nowrap"
                                    method="POST"
                                    action="{{ route('products.availability', $product->id()) }}"
                                    data-product-availability-form
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $product->isActive() ? '0' : '1' }}">
                                    <input
                                        type="checkbox"
                                        class="toggle toggle-success"
                                        @checked($product->isActive())
                                        aria-label="{{ $product->isActive() ? 'Desactivar producto' : 'Activar producto' }}"
                                        data-product-availability-toggle
                                    >
                                    <span class="badge {{ $product->isActive() ? 'badge-success' : 'badge-error' }}">
                                        {{ $product->isActive() ? 'Activo' : 'Inactivo' }}
                                    </span>
                                    <span
                                        class="loading loading-spinner loading-sm hidden"
                                        aria-label="Procesando"
                                        data-product-availability-spinner
                                    ></span>
                                </form>
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-outline" href="{{ route('products.edit', $product->id()) }}">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">
                                {{ $search !== '' ? 'No se encontraron productos.' : 'No hay productos cargados.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
