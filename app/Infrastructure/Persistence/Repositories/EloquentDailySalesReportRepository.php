<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Reports\DTOs\DailySalesReportQuery;
use App\Domain\Reports\Repositories\DailySalesReportRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use Carbon\CarbonImmutable;

final readonly class EloquentDailySalesReportRepository implements DailySalesReportRepositoryInterface
{
    public function dailySales(DailySalesReportQuery $query): array
    {
        $day = CarbonImmutable::parse($query->date);
        $orders = OrderModel::query()
            ->with('table')
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$day->startOfDay(), $day->endOfDay()])
            ->orderByDesc('closed_at')
            ->get();

        $totalInCents = (int) round(((float) $orders->sum('total')) * 100);
        $cashInCents = $this->sumByPaymentMethod($orders, 'cash');
        $transferInCents = $this->sumByPaymentMethod($orders, 'transfer');
        $cardInCents = $this->sumByPaymentMethod($orders, 'card');
        $ordersCount = $orders->count();

        return [
            'date' => $query->date,
            'totalInCents' => $totalInCents,
            'cashInCents' => $cashInCents,
            'transferInCents' => $transferInCents,
            'cardInCents' => $cardInCents,
            'closedTablesCount' => $ordersCount,
            'closedOrdersCount' => $ordersCount,
            'averageTicketInCents' => $ordersCount > 0 ? (int) round($totalInCents / $ordersCount) : 0,
            'orders' => $orders
                ->map(fn (OrderModel $order): array => [
                    'tableNumber' => (int) $order->table->number,
                    'paymentMethod' => (string) ($order->payment_method ?? ''),
                    'totalInCents' => (int) round(((float) $order->total) * 100),
                    'closedAt' => $order->closed_at?->format('H:i') ?? '',
                ])
                ->all(),
        ];
    }

    public function recentClosedTables(int $limit): array
    {
        return OrderModel::query()
            ->with('table')
            ->where('status', 'closed')
            ->orderByDesc('closed_at')
            ->limit($limit)
            ->get()
            ->map(fn (OrderModel $order): array => [
                'orderId' => (int) $order->id,
                'tableNumber' => (int) $order->table->number,
                'totalInCents' => (int) round(((float) $order->total) * 100),
                'closedAt' => $order->closed_at?->format('Y-m-d H:i:s') ?? '',
            ])
            ->all();
    }

    public function closedTableStats(?int $todayCount = null): array
    {
        $today = CarbonImmutable::today();
        $monthStart = $today->startOfMonth();
        $monthDays = max(1, $monthStart->diffInDays($today) + 1);
        $weekendDays = $this->operationalWeekendDays($monthStart, $today);

        $todayCount ??= $this->closedTablesForDate($today);
        $monthOrders = OrderModel::query()
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$monthStart->startOfDay(), $today->endOfDay()])
            ->get(['closed_at']);
        $monthCount = $monthOrders->count();
        $weekendCount = $monthOrders
            ->filter(fn (OrderModel $order): bool => in_array($order->closed_at?->dayOfWeekIso, [4, 5, 6, 7], true))
            ->count();

        return [
            'today' => $todayCount,
            'weekendAverage' => count($weekendDays) > 0 ? round($weekendCount / count($weekendDays), 1) : 0.0,
            'monthlyAverage' => round($monthCount / $monthDays, 1),
        ];
    }

    private function sumByPaymentMethod($orders, string $paymentMethod): int
    {
        return (int) round(((float) $orders
            ->where('payment_method', $paymentMethod)
            ->sum('total')) * 100);
    }

    private function closedTablesForDate(CarbonImmutable $date): int
    {
        return OrderModel::query()
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$date->startOfDay(), $date->endOfDay()])
            ->count();
    }

    /**
     * @return list<string>
     */
    private function operationalWeekendDays(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $days = [];

        for ($date = $from; $date->lte($to); $date = $date->addDay()) {
            if (in_array($date->dayOfWeekIso, [4, 5, 6, 7], true)) {
                $days[] = $date->toDateString();
            }
        }

        return $days;
    }
}
