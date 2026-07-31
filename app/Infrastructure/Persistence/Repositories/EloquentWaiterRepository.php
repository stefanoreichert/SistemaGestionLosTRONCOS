<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Waiter\Entities\Waiter;
use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;

final class EloquentWaiterRepository implements WaiterRepositoryInterface
{
    public function all(): array
    {
        return $this->toEntities(WaiterModel::query()->orderBy('name')->get()->all());
    }

    public function search(string $term): array
    {
        $models = WaiterModel::query()
            ->where(function ($query) use ($term): void {
                $query
                    ->where('name', 'like', '%'.$term.'%')
                    ->orWhere('employee_code', 'like', '%'.$term.'%')
                    ->orWhere('phone', 'like', '%'.$term.'%');
            })
            ->orderBy('name')
            ->get()
            ->all();

        return $this->toEntities($models);
    }

    public function active(): array
    {
        return $this->toEntities(
            WaiterModel::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->all(),
        );
    }

    public function findById(int $id): ?Waiter
    {
        $model = WaiterModel::query()->find($id);

        return $model instanceof WaiterModel ? $this->toEntity($model) : null;
    }

    public function getById(int $id): Waiter
    {
        return $this->toEntity(WaiterModel::query()->findOrFail($id));
    }

    public function save(Waiter $waiter): Waiter
    {
        $model = $waiter->id() !== null
            ? WaiterModel::query()->findOrFail($waiter->id())
            : new WaiterModel();

        $model->fill([
            'name' => $waiter->name(),
            'employee_code' => $waiter->employeeCode(),
            'phone' => $waiter->phone(),
            'is_active' => $waiter->isActive(),
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    /**
     * @param list<WaiterModel> $models
     * @return list<Waiter>
     */
    private function toEntities(array $models): array
    {
        return array_map(fn (WaiterModel $model): Waiter => $this->toEntity($model), $models);
    }

    private function toEntity(WaiterModel $model): Waiter
    {
        return new Waiter(
            id: (int) $model->id,
            name: (string) $model->name,
            employeeCode: $model->employee_code !== null ? (string) $model->employee_code : null,
            phone: $model->phone !== null ? (string) $model->phone : null,
            isActive: (bool) $model->is_active,
        );
    }
}
