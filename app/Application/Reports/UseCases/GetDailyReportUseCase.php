<?php

namespace App\Application\Reports\UseCases;

use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;

final readonly class GetDailyReportUseCase
{
    public function __construct(private OrderRepositoryInterface $orders)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $date): array
    {
        $isCurrentPeriod = $date === now()->toDateString();
        $report = $this->orders->dailyReport($date, $isCurrentPeriod);
        $report['orders'] = array_map(
            static fn (Order $order): array => [
                'tableNumber' => $order->tableNumber(),
                'closedAt' => $order->closedAt(),
                'itemsCount' => count($order->items()),
                'paymentMethod' => $order->paymentMethod(),
                'paymentMethodLabel' => self::paymentMethodLabel($order->paymentMethod()),
                'totalInCents' => $order->totalInCents(),
            ],
            $report['orders'],
        );
        $report['isCurrentPeriod'] = $isCurrentPeriod;

        return $report;
    }

    private static function paymentMethodLabel(?string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'cash' => 'Efectivo',
            'transfer' => 'Transferencia',
            'card' => 'Tarjeta',
            default => 'Sin dato',
        };
    }
}
