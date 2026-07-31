<?php

namespace App\Application\Waiter\UseCases;

use App\Application\Waiter\DTOs\WaiterInputDTO;
use App\Domain\Waiter\Entities\Waiter;
use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;

final readonly class UpdateWaiterUseCase
{
    public function __construct(private WaiterRepositoryInterface $waiters)
    {
    }

    public function execute(int $id, WaiterInputDTO $dto): Waiter
    {
        $waiter = $this->waiters->getById($id);
        $waiter->rename($dto->name);
        $waiter->changeEmployeeCode($dto->employeeCode);
        $waiter->changePhone($dto->phone);

        return $this->waiters->save($waiter);
    }
}
