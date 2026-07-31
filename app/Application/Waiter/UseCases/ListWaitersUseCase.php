<?php

namespace App\Application\Waiter\UseCases;

use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;

final readonly class ListWaitersUseCase
{
    public function __construct(private WaiterRepositoryInterface $waiters)
    {
    }

    /**
     * @return list<\App\Domain\Waiter\Entities\Waiter>
     */
    public function execute(?string $search = null): array
    {
        $term = trim((string) $search);

        return $term === ''
            ? $this->waiters->all()
            : $this->waiters->search($term);
    }
}
