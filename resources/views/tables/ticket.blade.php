@inject('ticketFormatter', 'App\Application\Table\Services\TicketFormatter')

@php
    $ticketText = $ticketFormatter->text($order);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Mesa {{ $order->tableNumber() }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background: #f5f6f8;
            color: #000;
            font-family: Consolas, "Courier New", "Liberation Mono", monospace;
            font-weight: 800;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .screen {
            width: 100%;
            display: grid;
            justify-items: center;
            gap: 12px;
            padding: 16px;
        }
        .no-print {
            width: min(100%, 360px);
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 14px;
            border: 1px solid #e4e7ec;
            border-radius: 6px;
            background: #fff;
            color: #101828;
            text-decoration: none;
            cursor: pointer;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
        }
        .btn.primary { background: #2563eb; border-color: #2563eb; color: #fff; }
        .ticket {
            width: 50mm;
            max-width: 50mm;
            margin: 0;
            padding: 2mm 1mm;
            background: #fff;
            border: 1px solid #d0d5dd;
            box-shadow: 0 10px 25px rgba(16, 24, 40, .12);
            color: #000;
            font-family: Consolas, "Courier New", "Liberation Mono", monospace;
            font-size: 11px;
            line-height: 1.22;
            font-weight: 900;
            height: auto;
            min-height: 0;
        }
        .ticket pre {
            margin: 0;
            white-space: pre;
            color: #000;
            font: inherit;
            font-weight: 900;
        }
        .total {
            display: block;
            font-size: 16px;
            line-height: 1.15;
            font-weight: 900;
        }

        @page {
            size: 58mm auto;
            margin: 0;
        }

        @media print {
            html, body {
                width: 58mm;
                margin: 0;
                padding: 0;
                background: #fff;
            }

            body {
                height: auto;
                color: #000;
                font-weight: 900;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .screen {
                display: block;
                width: 58mm;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .ticket {
                width: 50mm;
                max-width: 50mm;
                margin: 0;
                padding: 2mm 1mm;
                border: 0;
                box-shadow: none;
                color: #000;
                font-family: Consolas, "Courier New", "Liberation Mono", monospace;
                font-size: 11px;
                line-height: 1.22;
                font-weight: 900;
                height: auto;
                min-height: 0;
            }

            .ticket pre {
                font-weight: 900;
            }
        }
    </style>
</head>
<body>
    <main class="screen">
        <div class="no-print">
            <a class="btn" href="{{ $backUrl ?? route('tables.index') }}">Volver</a>
            <button class="btn primary" type="button" onclick="window.print()">Imprimir ticket</button>
        </div>

        <section class="ticket" aria-label="Ticket termico">
            <pre>{{ $ticketText }}</pre>
        </section>
    </main>
</body>
</html>
