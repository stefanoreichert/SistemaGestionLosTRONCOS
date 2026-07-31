<?php

namespace App\Application\Waiter\UseCases;

use App\Domain\Waiter\Entities\Waiter;
use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;

final readonly class SetWaiterAvailabilityUseCase
{
    public function __construct(private WaiterRepositoryInterface $waiters)
    {
    }

    public function execute(int $id, bool $isActive): Waiter
    {
        $waiter = $this->waiters->getById($id);

        if ($isActive) {
            $waiter->activate();
        } else {
            $waiter->deactivate();
        }

        return $this->waiters->save($waiter);
    }
}
