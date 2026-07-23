<x-layouts.app title="Editar producto">
    <div class="card">
        <div class="card-header"><strong>Editar producto</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product->id()) }}">
                @csrf
                @method('PUT')
                @include('products._form', ['product' => $product])
            </form>
        </div>
    </div>
</x-layouts.app>
