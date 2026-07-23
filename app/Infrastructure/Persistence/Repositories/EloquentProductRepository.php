<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Product\Entities\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function all(): array
    {
        return ProductModel::query()
            ->orderBy('name')
            ->get()
            ->map(fn (ProductModel $model): Product => $this->toEntity($model))
            ->all();
    }

    public function active(): array
    {
        return ProductModel::query()
            ->orderBy('name')
            ->get()
            ->map(fn (ProductModel $model): Product => $this->toEntity($model))
            ->all();
    }

    public function count(): int
    {
        return ProductModel::query()->count();
    }

    public function findById(int $id): ?Product
    {
        $model = ProductModel::query()->find($id);

        return $model instanceof ProductModel ? $this->toEntity($model) : null;
    }

    public function save(Product $product): Product
    {
        $model = $product->id() !== null
            ? ProductModel::query()->findOrFail($product->id())
            : new ProductModel();

        $model->fill([
            'name' => $product->name(),
            'category' => $product->category(),
            'price' => number_format($product->priceInCents() / 100, 2, '.', ''),
        ]);

        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        ProductModel::query()->findOrFail($id)->delete();
    }

    private function toEntity(ProductModel $model): Product
    {
        return new Product(
            id: (int) $model->id,
            name: (string) $model->name,
            priceInCents: (int) round(((float) $model->price) * 100),
            category: (string) $model->category,
        );
    }
}
