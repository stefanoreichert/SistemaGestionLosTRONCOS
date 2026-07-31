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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name() }}</strong>
                            </td>
                            <td>{{ $product->category() }}</td>
                            <td>${{ number_format($product->priceInCents() / 100, 0, ',', '.') }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-outline" href="{{ route('products.edit', $product->id()) }}">Editar</a>
                                    <form
                                        method="POST"
                                        action="{{ route('products.destroy', $product->id()) }}"
                                        onsubmit="return confirm('¿Está seguro de que desea eliminar este producto? Esta acción no se puede deshacer.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-error btn-outline danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">
                                {{ $search !== '' ? 'No se encontraron productos.' : 'No hay productos cargados.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
