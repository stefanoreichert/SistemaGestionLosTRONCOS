<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Table\Entities\Order;
use App\Domain\Table\Entities\OrderItem;
use App\Infrastructure\Persistence\Eloquent\Models\OrderItemModel;
use App\Infrastructure\Persistence\Eloquent\Models\OrderModel;

final class EloquentOrderMapper
{
    public function toEntity(OrderModel $model): Order
    {
        return new Order(
            id: (int) $model->id,
            tableId: (int) $model->table_id,
            tableNumber: (int) $model->table->number,
            status: (string) $model->status,
            subtotalInCents: (int) round(((float) $model->subtotal) * 100),
            totalInCents: (int) round(((float) $model->total) * 100),
            paymentMethod: $model->payment_method !== null ? (string) $model->payment_method : null,
            ticketNumber: $model->ticket_number !== null ? (string) $model->ticket_number : null,
            openedAt: $model->opened_at?->format('Y-m-d H:i:s') ?? '',
            closedAt: $model->closed_at?->format('Y-m-d H:i:s'),
            items: $model->items
                ->map(fn (OrderItemModel $item): OrderItem => new OrderItem(
                    id: (int) $item->id,
                    productId: (int) $item->product_id,
                    productName: (string) ($item->product?->name ?? 'Producto eliminado'),
                    category: (string) ($item->product?->category ?? 'Sin categoria'),
                    quantity: (int) $item->quantity,
                    unitPriceInCents: (int) round(((float) $item->unit_price) * 100),
                    subtotalInCents: (int) round(((float) $item->subtotal) * 100),
                ))
                ->all(),
        );
    }
}
