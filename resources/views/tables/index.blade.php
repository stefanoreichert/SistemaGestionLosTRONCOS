<x-layouts.app title="Mesas">
    @if (auth()->user()->isWaiter())
        <div
            id="waiter-kitchen-notifications"
            data-url="{{ route('tables.kitchen-notifications') }}"
            data-since="{{ $kitchenNotificationSince }}"
            class="mb-4 flex justify-end"
        >
            <button class="btn btn-outline btn-sm" type="button" data-enable-kitchen-sound>Activar sonido</button>
        </div>
    @endif
    <div class="rounded-box bg-base-200 p-4 sm:p-6" data-theme="emerald">
        <section class="card rounded-box border border-base-300 bg-base-100 shadow">
            <div class="card-body gap-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="card-title text-2xl">Gestion de Mesas</h2>
                        <p class="mt-1 text-sm text-base-content/60">Seleccioná una mesa para comenzar o ver su pedido.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-success gap-1">🟢 Libre</span>
                        <span class="badge badge-error gap-1">🔴 Ocupada</span>
                    </div>
                </div>

                <div class="divider my-0"></div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-5 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                    @foreach ($tables as $table)
                        <a
                            class="card h-56 cursor-pointer rounded-box border-2 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-xl {{ $table->isOccupied() ? '!border-error !bg-error/10' : '!border-success !bg-success/10' }}"
                            href="{{ route('tables.show', $table->number()) }}"
                        >
                            <div class="card-body items-center justify-between gap-2 p-4 text-center sm:p-5">
                                <h3 class="card-title text-xl sm:text-2xl">🍽️ Mesa {{ $table->number() }}</h3>

                                <div class="divider my-0"></div>

                                <span class="badge badge-lg {{ $table->isOccupied() ? 'badge-error' : 'badge-success' }}">
                                    {{ $table->isOccupied() ? '🔴 Ocupada' : '🟢 Libre' }}
                                </span>

                                @if ($table->isOccupied())
                                    <span class="text-sm font-medium text-base-content/70">
                                        {{ $table->openOrder()?->waiterName() !== null
                                            ? 'Atiende: '.$table->openOrder()->waiterName()
                                            : 'Mozo no asignado' }}
                                    </span>
                                @endif

                                <div class="divider my-0"></div>

                                <span class="btn w-full {{ $table->isOccupied() ? 'btn-error' : 'btn-success' }}">
                                    {{ $table->isOccupied() ? 'Ver Pedido' : 'Abrir Mesa' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
