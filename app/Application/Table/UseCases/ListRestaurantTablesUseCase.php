<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Repositories\RestaurantTableRepositoryInterface;

final readonly class ListRestaurantTablesUseCase
{
    public function __construct(private RestaurantTableRepositoryInterface $tables)
    {
    }

    public function execute(): array
    {
        return $this->tables->allWithOpenOrder();
    }
}
