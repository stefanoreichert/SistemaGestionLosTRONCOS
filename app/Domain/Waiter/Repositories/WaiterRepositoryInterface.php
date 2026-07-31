<?php

namespace App\Domain\Waiter\Repositories;

use App\Domain\Waiter\Entities\Waiter;

interface WaiterRepositoryInterface
{
    /**
     * @return list<Waiter>
     */
    public function all(): array;

    /**
     * @return list<Waiter>
     */
    public function search(string $term): array;

    /**
     * @return list<Waiter>
     */
    public function active(): array;

    public function findById(int $id): ?Waiter;

    public function getById(int $id): Waiter;

    public function save(Waiter $waiter): Waiter;
}
