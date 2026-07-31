<x-layouts.app title="Cambiar contraseña">
    <div data-theme="emerald" class="card bg-base-100 border border-base-300 shadow-sm max-w-2xl">
        <div class="card-body gap-5">
            <h2 class="card-title">Cambiar contraseña</h2>

            <form class="space-y-4" method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PATCH')

                <fieldset class="fieldset">
                    <label class="fieldset-label" for="current_password">Contraseña actual</label>
                    <input
                        class="input input-bordered w-full"
                        id="current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >
                    @error('current_password') <p class="text-error text-sm">{{ $message }}</p> @enderror
                </fieldset>

                <fieldset class="fieldset">
                    <label class="fieldset-label" for="password">Nueva contraseña</label>
                    <input
                        class="input input-bordered w-full"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                    @error('password') <p class="text-error text-sm">{{ $message }}</p> @enderror
                </fieldset>

                <fieldset class="fieldset">
                    <label class="fieldset-label" for="password_confirmation">Confirmar contraseña</label>
                    <input
                        class="input input-bordered w-full"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </fieldset>

                <div class="flex flex-wrap gap-3">
                    <button class="btn btn-primary" type="submit">Guardar contraseña</button>
                    <a class="btn btn-outline" href="{{ route('dashboard') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
