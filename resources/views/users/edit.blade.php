<x-layouts.app title="Editar usuario">
    <div data-theme="emerald" class="grid gap-5 lg:grid-cols-2">
        <div class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Datos del usuario</h2>
                <form class="space-y-4" method="POST" action="{{ route('users.update', $account) }}">
                    @csrf
                    @method('PUT')
                    @include('users._form', ['account' => $account])
                </form>
            </div>
        </div>

        <div id="password" class="card h-fit border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Cambiar contraseña</h2>
                <p class="text-sm text-base-content/70">La contraseña actual nunca se muestra ni se recupera.</p>
                <form class="space-y-4" method="POST" action="{{ route('users.password', $account) }}">
                    @csrf
                    @method('PATCH')
                    <fieldset class="fieldset">
                        <label class="fieldset-label" for="new_password">Nueva contraseña</label>
                        <input class="input input-bordered w-full" id="new_password" name="password" type="password" required autocomplete="new-password">
                    </fieldset>
                    <fieldset class="fieldset">
                        <label class="fieldset-label" for="new_password_confirmation">Confirmar contraseña</label>
                        <input class="input input-bordered w-full" id="new_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    </fieldset>
                    @error('password') <div class="alert alert-error" role="alert">{{ $message }}</div> @enderror
                    <button class="btn btn-warning" type="submit">Actualizar contraseña</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
