@php($isEdit = isset($account))

@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<fieldset class="fieldset">
    <label class="fieldset-label" for="name">Nombre</label>
    <input class="input input-bordered w-full" id="name" name="name" value="{{ old('name', $isEdit ? $account->name : '') }}" required>
</fieldset>

<fieldset class="fieldset">
    <label class="fieldset-label" for="phone">Teléfono</label>
    <input class="input input-bordered w-full" id="phone" name="phone" type="tel" value="{{ old('phone', $isEdit ? $account->phone : '') }}" maxlength="30">
</fieldset>

<fieldset class="fieldset">
    <label class="fieldset-label" for="email">Email</label>
    <input class="input input-bordered w-full" id="email" name="email" type="email" value="{{ old('email', $isEdit ? $account->email : '') }}" required autocomplete="off">
</fieldset>

@unless ($isEdit)
    <div class="grid gap-4 md:grid-cols-2">
        <fieldset class="fieldset">
            <label class="fieldset-label" for="password">Contraseña</label>
            <input class="input input-bordered w-full" id="password" name="password" type="password" required autocomplete="new-password">
        </fieldset>
        <fieldset class="fieldset">
            <label class="fieldset-label" for="password_confirmation">Confirmar contraseña</label>
            <input class="input input-bordered w-full" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
        </fieldset>
    </div>
@endunless

<fieldset class="fieldset">
    <label class="fieldset-label" for="role">Rol</label>
    <select class="select select-bordered w-full" id="role" name="role" required>
        @foreach ($roles as $role)
            <option value="{{ $role->value }}" @selected(old('role', $isEdit ? $account->role : '') === $role->value)>
                {{ $role->label() }} ({{ $role->value }})
            </option>
        @endforeach
    </select>
</fieldset>

<div class="flex flex-wrap gap-3 pt-2">
    <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Guardar cambios' : 'Crear usuario' }}</button>
    <a class="btn btn-outline" href="{{ route('users.index') }}">Cancelar</a>
</div>
