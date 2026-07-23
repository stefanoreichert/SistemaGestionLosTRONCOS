<?php

namespace App\Domain\Table\Repositories;

use App\Domain\Table\Entities\RestaurantTable;

interface RestaurantTableRepositoryInterface
{
    public function ensureRange(int $from, int $to): void;

    /**
     * @return list<RestaurantTable>
     */
    public function allWithOpenOrder(): array;

    public function findByNumberWithOpenOrder(int $number): ?RestaurantTable;

    public function countFree(): int;

    public function countOccupied(): int;
}
