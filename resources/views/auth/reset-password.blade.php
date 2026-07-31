<x-layouts.guest title="Restablecer contraseña">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body gap-5">
            <h2 class="card-title text-2xl">Restablecer contraseña</h2>

            <form class="space-y-4" method="POST" action="{{ route('password.store') }}">
                @csrf
                <input name="token" type="hidden" value="{{ $token }}">

                <fieldset class="fieldset">
                    <label class="fieldset-label" for="email">Correo electrónico</label>
                    <input
                        class="input input-bordered w-full"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $email) }}"
                        autocomplete="email"
                        required
                    >
                    @error('email') <p class="text-error text-sm">{{ $message }}</p> @enderror
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

                <button class="btn btn-primary w-full" type="submit">Restablecer contraseña</button>
            </form>
        </div>
    </div>
</x-layouts.guest>
