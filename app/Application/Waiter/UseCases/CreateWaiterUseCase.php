<?php

namespace App\Application\Waiter\UseCases;

use App\Application\Waiter\DTOs\WaiterInputDTO;
use App\Domain\Waiter\Entities\Waiter;
use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;

final readonly class CreateWaiterUseCase
{
    public function __construct(private WaiterRepositoryInterface $waiters)
    {
    }

    public function execute(WaiterInputDTO $dto): Waiter
    {
        return $this->waiters->save(new Waiter(
            id: null,
            name: $dto->name,
            employeeCode: $dto->employeeCode,
            phone: $dto->phone,
        ));
    }
}
