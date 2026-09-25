<?php

namespace Tests\Unit\Application\Table;

use App\Application\Table\Services\TicketFormatter;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Entities\OrderItem;
use Tests\TestCase;

final class TicketFormatterTest extends TestCase
{
    public function test_it_uses_the_simplified_header_and_preserves_ticket_details(): void
    {
        config([
            'restaurant.name' => 'LOS TRONCOS RESTO BAR',
            'restaurant.address' => 'Dirección que no debe imprimirse',
            'restaurant.city' => 'Ciudad que no debe imprimirse',
            'restaurant.phone' => '123456789',
            'restaurant.instagram' => '@contacto',
            'restaurant.ticket.width' => 26,
        ]);

        $lines = (new TicketFormatter)->lines($this->order());
        $detailStart = array_key_first(array_filter(
            $lines,
            static fn (string $line): bool => str_starts_with($line, 'Cant x Producto'),
        ));
        $headerAndOperation = array_slice($lines, 0, $detailStart === false ? 0 : $detailStart);
        $text = implode("\n", $lines);

        $this->assertSame('  LOS TRONCOS RESTO BAR', $lines[0]);
        $this->assertSame('    Ticket N.º 000123', $lines[1]);
        $this->assertSame('  NO VÁLIDO COMO FACTURA', $lines[2]);
        $this->assertStringNotContainsString('Dirección que no debe imprimirse', implode("\n", $headerAndOperation));
        $this->assertStringNotContainsString('Ciudad que no debe imprimirse', implode("\n", $headerAndOperation));
        $this->assertStringNotContainsString('123456789', implode("\n", $headerAndOperation));
        $this->assertStringNotContainsString('Instagram', implode("\n", $headerAndOperation));
        $this->assertStringContainsString('Mesa: 7', $text);
        $this->assertStringContainsString('2 x HAMBURGUESA', $text);
        $this->assertStringContainsString('Subtotal:', $text);
        $this->assertStringContainsString('Metodo:', $text);
        $this->assertStringContainsString('Efectivo', $text);
        $this->assertStringContainsString('$30.000', $text);

        foreach ($lines as $line) {
            $this->assertLessThanOrEqual(26, mb_strwidth($line), "La línea excede el ancho térmico: {$line}");
        }
    }

    private function order(): Order
    {
        return new Order(
            id: 123,
            tableId: 7,
            tableNumber: 7,
            status: 'closed',
            subtotalInCents: 3000000,
            totalInCents: 3000000,
            paymentMethod: 'cash',
            ticketNumber: '000123',
            waiterId: null,
            waiterName: null,
            openedAt: '2026-08-14 19:30:00',
            closedAt: '2026-08-14 20:15:00',
            items: [
                new OrderItem(
                    id: 1,
                    productId: 1,
                    productName: 'Hamburguesa',
                    category: 'Comidas',
                    quantity: 2,
                    unitPriceInCents: 1500000,
                    subtotalInCents: 3000000,
                ),
            ],
        );
    }
}
