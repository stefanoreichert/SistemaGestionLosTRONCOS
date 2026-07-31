<x-layouts.guest title="Ingresar">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body gap-5">
            <div>
                <h2 class="card-title text-2xl">Ingresar</h2>
                <p class="text-sm text-base-content/70">Acceda con su correo y contraseña.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('login.store') }}">
                @csrf

                <fieldset class="fieldset">
                    <label class="fieldset-label" for="email">Correo electrónico</label>
                    <input
                        class="input input-bordered w-full"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        autofocus
                        required
                    >
                    @error('email') <p class="text-error text-sm">{{ $message }}</p> @enderror
                </fieldset>

                <fieldset class="fieldset">
                    <label class="fieldset-label" for="password">Contraseña</label>
                    <input
                        class="input input-bordered w-full"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >
                    @error('password') <p class="text-error text-sm">{{ $message }}</p> @enderror
                </fieldset>

                <label class="label justify-start gap-3 cursor-pointer">
                    <input class="checkbox checkbox-primary" name="remember" type="checkbox" value="1">
                    <span class="label-text">Recordarme</span>
                </label>

                <button class="btn btn-primary w-full" type="submit">Ingresar</button>
            </form>

            <a class="link link-primary text-center text-sm" href="{{ route('password.request') }}">
                ¿Olvidó su contraseña?
            </a>
        </div>
    </div>
</x-layouts.guest>
