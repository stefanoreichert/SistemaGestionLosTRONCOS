<?php

namespace App\Infrastructure\Printing;

use App\Application\Table\Ports\TicketPrinterInterface;
use App\Application\Table\Services\TicketFormatter;
use App\Domain\Table\Entities\Order;
use Illuminate\Support\Facades\Log;

final readonly class TicketPrinterService implements TicketPrinterInterface
{
    public function __construct(private TicketFormatter $formatter)
    {
    }

    public function print(Order $order): void
    {
        $printer = config('restaurant.ticket.printer');

        if ($printer === null || $printer === '') {
            Log::info('Thermal printer not configured; using browser fallback ticket.', [
                'order_id' => $order->id(),
            ]);

            return;
        }

        $this->sendToPrinter((string) $printer, $this->formatter->text($order));
    }

    private function sendToPrinter(string $printer, string $ticket): void
    {
        $payload = "\x1B\x40".$ticket."\n\n\n\x1D\x56\x00";

        if (PHP_OS_FAMILY === 'Windows') {
            $path = str_starts_with($printer, '\\\\') ? $printer : '\\\\localhost\\'.$printer;
            $this->write($path, $payload);

            return;
        }

        $this->write($printer, $payload);
    }

    private function write(string $path, string $payload): void
    {
        $written = @file_put_contents($path, $payload);

        if ($written === false) {
            Log::warning('Thermal printer write failed; browser fallback ticket remains available.', [
                'printer' => $path,
            ]);
        }
    }
}
