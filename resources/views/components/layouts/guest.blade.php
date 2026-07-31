<!DOCTYPE html>
<html lang="es" data-theme="emerald">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Acceso' }} — Los Troncos Resto Bar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                <h1 class="text-3xl font-bold text-primary">Los Troncos</h1>
                <p class="text-sm text-base-content/70">Resto Bar</p>
            </div>

            {{ $slot }}
        </div>
    </main>
</body>
</html>
