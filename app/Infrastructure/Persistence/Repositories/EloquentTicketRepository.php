<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Tickets\DTOs\TicketFiltersDTO;
use App\Domain\Table\Entities\Order;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final readonly class EloquentTicketRepository implements TicketRepositoryInterface
{
    public function __construct(private EloquentOrderMapper $mapper)
    {
    }

    public function assignNextTicketNumber(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId): Order {
            $order = OrderModel::query()
                ->with(['table', 'items.product'])
                ->where('status', 'closed')
                ->lockForUpdate()
                ->findOrFail($orderId);

            if ($order->ticket_number === null) {
                $order->ticket_number = $this->nextTicketNumber();
                $order->save();
            }

            return $this->freshOrder($order);
        });
    }

    public function findClosedById(int $orderId): ?Order
    {
        $order = OrderModel::query()
            ->with(['table', 'items.product'])
            ->where('status', 'closed')
            ->find($orderId);

        return $order instanceof OrderModel ? $this->mapper->toEntity($order) : null;
    }

    public function listClosed(TicketFiltersDTO $filters): array
    {
        return OrderModel::query()
            ->with(['table', 'items.product'])
            ->where('status', 'closed')
            ->when($filters->from !== null, fn (Builder $query) => $query->whereDate('closed_at', '>=', $filters->from))
            ->when($filters->to !== null, fn (Builder $query) => $query->whereDate('closed_at', '<=', $filters->to))
            ->when($filters->tableNumber !== null, fn (Builder $query) => $query->whereHas('table', fn (Builder $tableQuery) => $tableQuery->where('number', $filters->tableNumber)))
            ->when($filters->paymentMethod !== null, fn (Builder $query) => $query->where('payment_method', $filters->paymentMethod))
            ->orderByDesc('closed_at')
            ->limit(200)
            ->get()
            ->map(fn (OrderModel $order): Order => $this->mapper->toEntity($order))
            ->all();
    }

    private function nextTicketNumber(): string
    {
        $last = OrderModel::query()
            ->whereNotNull('ticket_number')
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $next = ((int) $last) + 1;

        return str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    private function freshOrder(OrderModel $order): Order
    {
        $fresh = OrderModel::query()->with(['table', 'items.product'])->findOrFail($order->id);

        return $this->mapper->toEntity($fresh);
    }
}
