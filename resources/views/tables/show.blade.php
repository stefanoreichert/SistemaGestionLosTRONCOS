<x-layouts.app title="Mesa {{ $table->number() }}">
    @if ($isAssignedToAnotherWaiter)
        <div class="alert alert-warning mb-4" role="alert">
            <span>Esta mesa está siendo atendida por {{ $table->openOrder()->waiterName() }}.</span>
        </div>
    @endif

    <div class="grid two-columns table-workspace">
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-header">
                <div>
                    <strong>Productos disponibles</strong>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="badge {{ $table->isOccupied() ? 'badge-error' : 'badge-success' }}">
                            {{ $table->isOccupied() ? 'Ocupada' : 'Libre' }}
                        </span>
                        <span class="badge badge-outline">
                            {{ $table->openOrder()?->waiterName() !== null
                                ? 'Atiende: '.$table->openOrder()->waiterName()
                                : 'Mozo no asignado' }}
                        </span>
                        @if ($table->openOrder()?->kitchenStatus() !== null)
                            <span class="badge badge-info badge-outline">
                                Cocina: {{ str_replace('_', ' ', $table->openOrder()->kitchenStatus()) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($canAddProducts)
                <form class="product-search" method="POST" action="{{ route('tables.products.search', $table->number()) }}">
                    @csrf
                    <div class="product-search-field">
                        <label for="product_name">Buscar producto</label>
                        <input
                            class="input input-bordered"
                            id="product_name"
                            name="product_name"
                            value="{{ old('product_name') }}"
                            placeholder="Escribi el nombre y presiona Enter"
                            autocomplete="off"
                            autofocus
                        >
                        <div id="product_suggestions" class="search-suggestions"></div>
                        @error('product_name') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn primary btn-primary" type="submit">Agregar</button>
                </form>

                <div class="product-list" id="product_list">
                    @forelse ($productsByCategory as $category => $categoryProducts)
                        <h3 class="product-category mt-[14px] mb-1 text-base" data-category="{{ $category }}">{{ $category }}</h3>
                        @foreach ($categoryProducts as $product)
                            <form
                                class="product-row"
                                data-product-name="{{ $product->name() }}"
                                data-product-category="{{ $category }}"
                                method="POST"
                                action="{{ route('tables.products.store', $table->number()) }}"
                            >
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id() }}">
                                <div>
                                    <strong>{{ $product->name() }}</strong>
                                    <div class="muted">${{ number_format($product->priceInCents() / 100, 0, ',', '.') }}</div>
                                </div>
                                <button class="btn primary btn-primary" type="submit">Agregar</button>
                            </form>
                        @endforeach
                    @empty
                        <div class="muted">No hay productos activos disponibles.</div>
                    @endforelse
                    <div class="muted" id="product_empty_search" style="display:none;">No hay productos que coincidan.</div>
                </div>
                @else
                    <div class="alert alert-info">
                        <span>Los productos disponibles están en modo de solo lectura.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-header"><strong>Consumo actual</strong></div>
            <div class="card-body !p-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Subtotal</th>
                            @if ($canModifyOrder)
                                <th>Quitar</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($table->openOrder())
                            @forelse ($table->openOrder()->items() as $item)
                                <tr>
                                    <td>{{ $item->productName() }}</td>
                                    <td>
                                        @if ($canModifyOrder)
                                            <form class="quantity-form" method="POST" action="{{ route('tables.products.quantity', $table->number()) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="product_id" value="{{ $item->productId() }}">
                                                <input class="quantity-input input input-bordered" type="number" name="quantity" value="{{ $item->quantity() }}" min="1" max="999">
                                            </form>
                                        @else
                                            <span>{{ $item->quantity() }}</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($item->subtotalInCents() / 100, 0, ',', '.') }}</td>
                                    @if ($canModifyOrder)
                                        <td>
                                            <form
                                                method="POST"
                                                action="{{ route('tables.products.destroy', $table->number()) }}"
                                                data-confirm-remove-product
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="product_id" value="{{ $item->productId() }}">
                                                <button class="btn danger btn-error btn-outline" type="submit">Quitar</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ $canModifyOrder ? 4 : 3 }}" class="muted">La mesa no tiene productos cargados.</td></tr>
                            @endforelse
                            <tr>
                                <td colspan="2"><strong>Total</strong></td>
                                <td colspan="{{ $canModifyOrder ? 2 : 1 }}"><strong>${{ number_format($table->openOrder()->totalInCents() / 100, 0, ',', '.') }}</strong></td>
                            </tr>
                        @else
                            <tr><td colspan="{{ $canModifyOrder ? 4 : 3 }}" class="muted">La mesa no tiene pedido abierto.</td></tr>
                        @endif
                    </tbody>
                </table>
                <div class="p-4">
                    @if ($canModifyOrder)
                    <form method="POST" action="{{ route('tables.close', $table->number()) }}">
                        @csrf
                        <label for="payment_method">Método de pago</label>
                        <select class="select select-bordered mb-3" id="payment_method" name="payment_method" required>
                            <option value="">Seleccionar metodo</option>
                            <option value="cash">Efectivo</option>
                            <option value="transfer">Transferencia</option>
                            <option value="card">Tarjeta</option>
                        </select>
                        @error('payment_method') <div class="error mb-3">{{ $message }}</div> @enderror
                        <button class="btn danger full btn-error btn-outline" type="submit">Cerrar y liberar mesa</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $productSuggestions = array_map(static fn ($product): array => [
            'name' => $product->name(),
            'category' => $product->category(),
            'price' => number_format($product->priceInCents() / 100, 0, ',', '.'),
        ], $products);
    @endphp

    <script>
        (() => {
            const products = @json($productSuggestions);
            const input = document.getElementById('product_name');
            const suggestions = document.getElementById('product_suggestions');
            const productRows = [...document.querySelectorAll('.product-row')];
            const productCategories = [...document.querySelectorAll('.product-category')];
            const emptySearch = document.getElementById('product_empty_search');
            const quantityInputs = [...document.querySelectorAll('.quantity-input')];
            let activeSuggestionIndex = 0;

            if (!input || !suggestions) {
                return;
            }

            const normalize = (value) => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            const matchesSearch = (value, search) => normalize(value).includes(search);
            const suggestionButtons = () => [...suggestions.querySelectorAll('.search-suggestion')];
            const updateActiveSuggestion = (index) => {
                const buttons = suggestionButtons();

                if (buttons.length === 0) {
                    activeSuggestionIndex = 0;
                    return;
                }

                activeSuggestionIndex = (index + buttons.length) % buttons.length;
                buttons.forEach((button, buttonIndex) => {
                    button.classList.toggle('active', buttonIndex === activeSuggestionIndex);
                });
            };
            const submitSuggestion = (button) => {
                if (!button) {
                    return;
                }

                input.value = button.dataset.productName ?? input.value;
                suggestions.classList.remove('visible');
                input.form?.requestSubmit();
            };
            const filterProductList = (search) => {
                let visibleCount = 0;
                const visibleCategories = new Set();

                productRows.forEach((row) => {
                    const productName = row.dataset.productName ?? '';
                    const productCategory = row.dataset.productCategory ?? '';
                    const isVisible = search === '' || matchesSearch(productName, search);

                    row.style.display = isVisible ? '' : 'none';

                    if (isVisible) {
                        visibleCount++;
                        visibleCategories.add(productCategory);
                    }
                });

                productCategories.forEach((category) => {
                    category.style.display = visibleCategories.has(category.dataset.category ?? '') ? '' : 'none';
                });

                if (emptySearch) {
                    emptySearch.style.display = search !== '' && visibleCount === 0 ? '' : 'none';
                }
            };

            const render = () => {
                const search = normalize(input.value.trim());
                suggestions.innerHTML = '';
                filterProductList(search);

                if (search.length < 2) {
                    suggestions.classList.remove('visible');
                    activeSuggestionIndex = 0;
                    return;
                }

                const matches = products
                    .filter((product) => matchesSearch(product.name, search))
                    .slice(0, 8);

                if (matches.length === 0) {
                    suggestions.classList.remove('visible');
                    activeSuggestionIndex = 0;
                    return;
                }

                matches.forEach((product, index) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'search-suggestion';
                    button.dataset.productName = product.name;

                    const label = document.createElement('span');
                    label.textContent = product.name;

                    const category = document.createElement('small');
                    category.textContent = product.category;
                    label.appendChild(category);

                    const price = document.createElement('span');
                    price.className = 'search-suggestion-price';
                    price.textContent = `$${product.price}`;

                    button.appendChild(label);
                    button.appendChild(price);
                    button.addEventListener('click', () => submitSuggestion(button));
                    button.addEventListener('mouseenter', () => updateActiveSuggestion(index));

                    suggestions.appendChild(button);
                });

                updateActiveSuggestion(0);
                suggestions.classList.add('visible');
            };

            input.addEventListener('input', render);
            input.addEventListener('focus', render);
            input.addEventListener('keydown', (event) => {
                const buttons = suggestionButtons();

                if (event.key === 'ArrowDown' && buttons.length > 0) {
                    event.preventDefault();
                    updateActiveSuggestion(activeSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp' && buttons.length > 0) {
                    event.preventDefault();
                    updateActiveSuggestion(activeSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter' && buttons.length > 0) {
                    event.preventDefault();
                    submitSuggestion(buttons[activeSuggestionIndex] ?? buttons[0]);
                }
            });
            quantityInputs.forEach((quantityInput) => {
                quantityInput.addEventListener('change', () => {
                    quantityInput.form?.requestSubmit();
                });
                quantityInput.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        quantityInput.form?.requestSubmit();
                    }
                });
            });
            document.addEventListener('click', (event) => {
                if (!suggestions.contains(event.target) && event.target !== input) {
                    suggestions.classList.remove('visible');
                }
            });
        })();
    </script>
</x-layouts.app>
