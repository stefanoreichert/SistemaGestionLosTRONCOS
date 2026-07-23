<?php

namespace App\Application\Table\Services;

use App\Domain\Table\Entities\Order;
use App\Domain\Table\Entities\OrderItem;

final readonly class TicketFormatter
{
    /**
     * @return list<string>
     */
    public function lines(Order $order): array
    {
        $width = $this->width();
        $lines = [];

        $lines[] = $this->center($this->restaurant('name'), $width);
        $lines[] = $this->center($this->restaurant('address'), $width);
        $lines[] = $this->center($this->restaurant('city'), $width);
        $lines[] = $this->center('Tel: '.$this->restaurant('phone'), $width);
        $lines[] = $this->center('Instagram:', $width);
        $lines[] = $this->center($this->restaurant('instagram'), $width);
        $lines[] = $this->separator($width);
        $lines[] = $this->center('Comprobante No Valido', $width);
        $lines[] = $this->center('como Factura', $width);
        $lines[] = $this->separator($width);

        $closedAt = $order->closedAt() ?? $order->openedAt();
        $lines[] = 'Fecha: '.substr($closedAt, 0, 10);
        $lines[] = 'Hora:  '.substr($closedAt, 11, 8);
        $lines[] = 'Ticket: '.$this->ticketNumber($order);
        $lines[] = 'Mesa: '.$order->tableNumber();
        $lines[] = $this->separator($width);
        $lines[] = $this->columns('Cant x Producto', 'Total', $width);
        $lines[] = $this->separator($width);

        foreach ($order->items() as $item) {
            array_push($lines, ...$this->itemLines($item, $width));
        }

        $lines[] = $this->separator($width);
        $lines[] = $this->columns('Subtotal:', $this->money($order->subtotalInCents()), $width);
        $lines[] = $this->columns('Metodo:', $this->paymentMethod($order->paymentMethod()), $width);
        $lines[] = $this->columns('Entregado:', '-', $width);
        $lines[] = $this->columns('Vuelto:', '-', $width);
        $lines[] = str_repeat('=', $width);
        $lines[] = $this->center('TOTAL', $width);
        $lines[] = $this->center($this->money($order->totalInCents()), $width);
        $lines[] = str_repeat('=', $width);
        $lines[] = $this->center('Muchas gracias', $width);
        $lines[] = $this->center('por elegirnos.', $width);
        $lines[] = $this->center('Instagram', $width);
        $lines[] = $this->center($this->restaurant('instagram'), $width);
        $lines[] = $this->center('WhatsApp', $width);
        $lines[] = $this->center($this->restaurant('whatsapp'), $width);
        $lines[] = $this->separator($width);

        return $lines;
    }

    public function text(Order $order): string
    {
        return implode(PHP_EOL, $this->lines($order)).PHP_EOL;
    }

    private function width(): int
    {
        return max(24, (int) config('restaurant.ticket.width', 32));
    }

    private function restaurant(string $key): string
    {
        return $this->ascii((string) config('restaurant.'.$key, ''));
    }

    private function ticketNumber(Order $order): string
    {
        return $order->ticketNumber() ?? str_pad((string) ($order->id() ?? 0), 6, '0', STR_PAD_LEFT);
    }

    private function separator(int $width): string
    {
        return str_repeat('-', $width);
    }

    private function center(string $text, int $width): string
    {
        $text = $this->clip($this->ascii($text), $width);
        $padding = max(0, $width - strlen($text));

        return str_repeat(' ', intdiv($padding, 2)).$text;
    }

    private function columns(string $left, string $right, int $width): string
    {
        $left = $this->ascii($left);
        $right = $this->ascii($right);
        $space = max(1, $width - strlen($right) - 1);

        return str_pad($this->clip($left, $space), $space, ' ').' '.$right;
    }

    /**
     * @return list<string>
     */
    private function itemLines(OrderItem $item, int $width): array
    {
        $amount = $this->money($item->subtotalInCents());
        $prefix = $item->quantity().' x ';
        $name = strtoupper($this->ascii($item->productName()));
        $firstWidth = max(8, $width - strlen($amount) - 1);
        $nameWidth = max(1, $firstWidth - strlen($prefix));
        $chunks = $this->wrap($name, $nameWidth);
        $lines = [];

        $lines[] = str_pad($prefix.($chunks[0] ?? ''), $firstWidth, ' ').' '.$amount;

        foreach (array_slice($chunks, 1) as $chunk) {
            $lines[] = str_repeat(' ', strlen($prefix)).$chunk;
        }

        return $lines;
    }

    /**
     * @return list<string>
     */
    private function wrap(string $text, int $width): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            if ($line === '') {
                $line = $this->clip($word, $width);
                continue;
            }

            if (strlen($line.' '.$word) <= $width) {
                $line .= ' '.$word;
            } else {
                $lines[] = $line;
                $line = $this->clip($word, $width);
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return $lines === [] ? [''] : $lines;
    }

    private function money(int $amountInCents): string
    {
        return '$'.number_format($amountInCents / 100, 0, ',', '.');
    }

    private function paymentMethod(?string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'cash' => 'Efectivo',
            'transfer' => 'Transferencia',
            'card' => 'Tarjeta',
            default => 'Sin dato',
        };
    }

    private function clip(string $text, int $width): string
    {
        return strlen($text) <= $width ? $text : substr($text, 0, $width);
    }

    private function ascii(string $text): string
    {
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return $converted !== false ? $converted : $text;
    }
}
