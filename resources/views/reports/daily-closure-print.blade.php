<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resumen Diario - {{ $closure->closedAt() }}</title>
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/js/app.js'])
</head>
<body class="daily-closure-print-page" data-daily-closure-print>
    <main class="daily-closure-print">
        <header class="daily-closure-print-header">
            <h1>Los Troncos Resto Bar</h1>
            <h2>Resumen Diario</h2>
            <p><strong>Inicio del período:</strong> {{ $closure->periodStartedAt() }}</p>
            <p><strong>Fecha y hora del cierre:</strong> {{ $closure->closedAt() }}</p>
        </header>

        @include('reports.partials.daily-summary', ['report' => $report])

        <div class="daily-closure-print-actions">
            <button class="btn btn-primary" type="button" data-print-daily-closure>Imprimir</button>
            <a class="btn btn-outline" href="{{ route('reports.daily') }}">Volver al Resumen Diario</a>
        </div>
    </main>
</body>
</html>
