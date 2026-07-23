<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Table\Entities\RestaurantTable;
use App\Domain\Table\Repositories\RestaurantTableRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\TableModel;

final class EloquentRestaurantTableRepository implements RestaurantTableRepositoryInterface
{
    public function __construct(private readonly EloquentOrderMapper $orders)
    {
    }

    public function ensureRange(int $from, int $to): void
    {
        for ($number = $from; $number <= $to; $number++) {
            TableModel::query()->firstOrCreate(['number' => $number]);
        }
    }

    public function allWithOpenOrder(): array
    {
        return TableModel::query()
            ->with('openOrder.items.product')
            ->orderBy('number')
            ->get()
            ->map(fn (TableModel $model): RestaurantTable => $this->toEntity($model))
            ->all();
    }

    public function findByNumberWithOpenOrder(int $number): ?RestaurantTable
    {
        $model = TableModel::query()
            ->with('openOrder.items.product')
            ->where('number', $number)
            ->first();

        return $model instanceof TableModel ? $this->toEntity($model) : null;
    }

    public function countFree(): int
    {
        return TableModel::query()->count() - $this->countOccupied();
    }

    public function countOccupied(): int
    {
        return TableModel::query()
            ->whereHas('openOrder.items')
            ->count();
    }

    private function toEntity(TableModel $model): RestaurantTable
    {
        return new RestaurantTable(
            id: (int) $model->id,
            number: (int) $model->number,
            openOrder: $model->openOrder !== null ? $this->orders->toEntity($model->openOrder) : null,
        );
    }
}
