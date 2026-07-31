<x-layouts.guest title="Recuperar contraseña">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body gap-5">
            <div>
                <h2 class="card-title text-2xl">Recuperar contraseña</h2>
                <p class="text-sm text-base-content/70">
                    Ingrese su correo y le enviaremos un enlace para restablecerla.
                </p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('password.email') }}">
                @csrf
                <fieldset class="fieldset">
                    <label class="fieldset-label" for="email">Correo electrónico</label>
                    <input
                        class="input input-bordered w-full"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        required
                    >
                    @error('email') <p class="text-error text-sm">{{ $message }}</p> @enderror
                </fieldset>

                <button class="btn btn-primary w-full" type="submit">Enviar enlace</button>
            </form>

            <a class="btn btn-ghost" href="{{ route('login') }}">Volver al ingreso</a>
        </div>
    </div>
</x-layouts.guest>
