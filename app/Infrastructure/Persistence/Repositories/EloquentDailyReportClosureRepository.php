<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reports\Entities\DailyReportClosure;
use App\Domain\Reports\Exceptions\EmptyDailyReportPeriod;
use App\Domain\Reports\Repositories\DailyReportClosureRepositoryInterface;
use App\Domain\Table\Entities\Order;
use App\Domain\Table\Repositories\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\DailyReportClosureModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use Illuminate\Support\Facades\DB;

final readonly class EloquentDailyReportClosureRepository implements DailyReportClosureRepositoryInterface
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function closeCurrentPeriod(int $userId, string $idempotencyKey): DailyReportClosure
    {
        return DB::transaction(function () use ($userId, $idempotencyKey): DailyReportClosure {
            $existing = DailyReportClosureModel::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing instanceof DailyReportClosureModel) {
                return $this->toEntity($existing);
            }

            $closedAt = now();
            $dayStart = $closedAt->copy()->startOfDay();
            $latestClosure = DailyReportClosureModel::query()
                ->whereBetween('closed_at', [$dayStart, $closedAt])
                ->orderByDesc('closed_at')
                ->lockForUpdate()
                ->first();

            $orderIds = OrderModel::query()
                ->where('status', 'closed')
                ->whereNull('daily_report_closure_id')
                ->whereBetween('closed_at', [$dayStart, $closedAt])
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->map(static fn ($id): int => (int) $id)
                ->all();

            $existing = DailyReportClosureModel::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing instanceof DailyReportClosureModel) {
                return $this->toEntity($existing);
            }

            if ($orderIds === []) {
                throw new EmptyDailyReportPeriod();
            }

            $report = $this->orders->dailyReportForOrderIds($closedAt->toDateString(), $orderIds);
            $snapshot = $this->snapshot($report);
            $closure = DailyReportClosureModel::query()->create([
                'period_started_at' => $latestClosure?->closed_at ?? $dayStart,
                'closed_at' => $closedAt,
                'closed_by_user_id' => $userId,
                'idempotency_key' => $idempotencyKey,
                'report_snapshot' => $snapshot,
            ]);

            OrderModel::query()
                ->whereIn('id', $orderIds)
                ->whereNull('daily_report_closure_id')
                ->update(['daily_report_closure_id' => $closure->id]);

            return $this->toEntity($closure);
        });
    }

    public function findById(int $id): ?DailyReportClosure
    {
        $closure = DailyReportClosureModel::query()->find($id);

        return $closure instanceof DailyReportClosureModel
            ? $this->toEntity($closure)
            : null;
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    private function snapshot(array $report): array
    {
        $report['orders'] = array_map(
            static fn (Order $order): array => [
                'id' => $order->id(),
                'tableNumber' => $order->tableNumber(),
                'closedAt' => $order->closedAt(),
                'itemsCount' => count($order->items()),
                'paymentMethod' => $order->paymentMethod(),
                'paymentMethodLabel' => self::paymentMethodLabel($order->paymentMethod()),
                'totalInCents' => $order->totalInCents(),
            ],
            $report['orders'],
        );

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

    private function toEntity(DailyReportClosureModel $closure): DailyReportClosure
    {
        return new DailyReportClosure(
            id: (int) $closure->id,
            periodStartedAt: $closure->period_started_at->format('Y-m-d H:i:s'),
            closedAt: $closure->closed_at->format('Y-m-d H:i:s'),
            closedByUserId: (int) $closure->closed_by_user_id,
            report: (array) $closure->report_snapshot,
        );
    }
}
