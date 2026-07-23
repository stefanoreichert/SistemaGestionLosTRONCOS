<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Entities\RestaurantTable;
use App\Domain\Table\Repositories\RestaurantTableRepositoryInterface;

final readonly class GetRestaurantTableUseCase
{
    public function __construct(private RestaurantTableRepositoryInterface $tables)
    {
    }

    public function execute(int $number): ?RestaurantTable
    {
        return $this->tables->findByNumberWithOpenOrder($number);
    }
}
