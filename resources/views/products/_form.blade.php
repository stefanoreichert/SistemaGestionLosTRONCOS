@php
    $isEdit = isset($product);
@endphp

<div class="form-grid">
    <div class="field">
        <label for="name">Nombre</label>
        <input class="input input-bordered" id="name" name="name" value="{{ old('name', $isEdit ? $product->name() : '') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="category">Categoria</label>
        <select class="select select-bordered" id="category" name="category" required>
            <option value="" disabled @selected(old('category', $isEdit ? $product->category() : '') === '')>
                Seleccione una categoría
            </option>
            @foreach ($categories as $category)
                <option
                    value="{{ $category }}"
                    @selected(old('category', $isEdit ? $product->category() : '') === $category)
                >
                    {{ $category }}
                </option>
            @endforeach
        </select>
        @error('category') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="price">Precio</label>
        <input class="input input-bordered" id="price" name="price" type="number" min="1" step="1" value="{{ old('price', $isEdit ? $product->priceInCents() / 100 : '') }}" required>
        @error('price') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label class="label cursor-pointer justify-start gap-3" for="requires_kitchen">
            <input type="hidden" name="requires_kitchen" value="0">
            <input class="toggle toggle-success" id="requires_kitchen" name="requires_kitchen" type="checkbox" value="1" @checked((bool) old('requires_kitchen', $isEdit ? $product->requiresKitchen() : true))>
            <span>Requiere preparación en cocina</span>
        </label>
        @error('requires_kitchen') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="flex gap-[10px] mt-[18px]">
    <button class="btn primary btn-primary" type="submit">{{ $isEdit ? 'Guardar cambios' : 'Crear producto' }}</button>
    <a class="btn btn-outline" href="{{ route('products.index') }}">Cancelar</a>
</div>
