<x-layouts.kitchen title="Panel de cocina">
    <section id="kitchen-panel" data-orders-url="{{ route('kitchen.orders') }}" data-update-url="{{ route('kitchen.orders.status', ['order' => '__ORDER__']) }}" data-csrf="{{ csrf_token() }}">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div><h1 class="text-3xl font-bold">Pedidos en cocina</h1><p class="text-base-content/60">Actualización automática cada 5 segundos</p></div>
            <div class="text-right"><div id="kitchen-clock" class="font-mono text-2xl font-bold"></div><div id="kitchen-date" class="text-sm text-base-content/60"></div></div>
        </div>
        <div id="kitchen-error" class="alert alert-error mb-4 hidden" role="alert"></div>
        <div id="kitchen-orders" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"></div>
        <div id="kitchen-empty" class="hero min-h-64 rounded-box bg-base-100"><div class="hero-content text-center"><div><h2 class="text-2xl font-bold">Sin pedidos pendientes</h2><p class="text-base-content/60">Los nuevos pedidos aparecerán automáticamente.</p></div></div></div>
    </section>
</x-layouts.kitchen>
