@php
    $isEdit = isset($waiter);
@endphp

<div class="form-grid">
    <div class="field">
        <label for="name">Nombre</label>
        <input
            class="input input-bordered"
            id="name"
            name="name"
            value="{{ old('name', $isEdit ? $waiter->name() : '') }}"
            maxlength="120"
            required
        >
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="employee_code">Código</label>
        <input
            class="input input-bordered"
            id="employee_code"
            name="employee_code"
            value="{{ old('employee_code', $isEdit ? $waiter->employeeCode() : '') }}"
            maxlength="50"
        >
        @error('employee_code') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="phone">Teléfono</label>
        <input
            class="input input-bordered"
            id="phone"
            name="phone"
            type="tel"
            value="{{ old('phone', $isEdit ? $waiter->phone() : '') }}"
            maxlength="30"
        >
        @error('phone') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="flex gap-[10px] mt-[18px]">
    <button class="btn primary btn-primary" type="submit">{{ $isEdit ? 'Guardar cambios' : 'Crear mozo' }}</button>
    <a class="btn btn-outline" href="{{ route('waiters.index') }}">Cancelar</a>
</div>
