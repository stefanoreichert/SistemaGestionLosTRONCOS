<!DOCTYPE html>
<html lang="es" data-theme="emerald">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cocina' }} | Los Troncos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
    <header class="navbar sticky top-0 z-20 border-b border-base-300 bg-base-100 shadow-sm">
        <div class="flex-1"><span class="text-xl font-bold">Los Troncos · Cocina</span></div>
        <div class="flex-none gap-2">
            <button class="btn btn-outline btn-sm" id="kitchen-enable-sound" type="button">Activar sonido</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('password.edit') }}">Cambiar contraseña</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline btn-sm" type="submit">Cerrar sesión</button></form>
        </div>
    </header>
    <main class="p-4 lg:p-6">{{ $slot }}</main>
</body>
</html>
