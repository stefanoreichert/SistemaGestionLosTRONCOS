@php
    $isEdit = isset($product);
@endphp

<div class="form-grid">
    <div class="field">
        <label for="name">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $isEdit ? $product->name() : '') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="category">Categoria</label>
        <input id="category" name="category" value="{{ old('category', $isEdit ? $product->category() : '') }}" required>
        @error('category') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="price">Precio</label>
        <input id="price" name="price" type="number" min="1" step="1" value="{{ old('price', $isEdit ? $product->priceInCents() / 100 : '') }}" required>
        @error('price') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div style="display:flex;gap:10px;margin-top:18px;">
    <button class="btn primary" type="submit">{{ $isEdit ? 'Guardar cambios' : 'Crear producto' }}</button>
    <a class="btn" href="{{ route('products.index') }}">Cancelar</a>
</div>
