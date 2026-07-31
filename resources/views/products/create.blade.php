<x-layouts.app title="Crear producto">
    <div class="card bg-base-100 border border-base-300 shadow-sm">
        <div class="card-header"><strong>Nuevo producto</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                @include('products._form')
            </form>
        </div>
    </div>
</x-layouts.app>
