<?php

namespace App\Application\Waiter\UseCases;

use App\Domain\Waiter\Entities\Waiter;
use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;

final readonly class GetWaiterUseCase
{
    public function __construct(private WaiterRepositoryInterface $waiters)
    {
    }

    public function execute(int $id): ?Waiter
    {
        return $this->waiters->findById($id);
    }
}
