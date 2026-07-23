<?php

namespace App\Domain\Product\Entities;

final class Product
{
    public function __construct(
        private readonly ?int $id,
        private string $name,
        private int $priceInCents,
        private string $category,
    ) {
        $this->rename($name);
        $this->changePrice($priceInCents);
        $this->changeCategory($category);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function priceInCents(): int
    {
        return $this->priceInCents;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function rename(string $name): void
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('El nombre del producto es obligatorio.');
        }

        $this->name = $name;
    }

    public function changePrice(int $priceInCents): void
    {
        if ($priceInCents <= 0) {
            throw new \InvalidArgumentException('El precio debe ser mayor a cero.');
        }

        $this->priceInCents = $priceInCents;
    }

    public function changeCategory(string $category): void
    {
        $category = trim($category);

        if ($category === '') {
            throw new \InvalidArgumentException('La categoria del producto es obligatoria.');
        }

        $this->category = $category;
    }

}
