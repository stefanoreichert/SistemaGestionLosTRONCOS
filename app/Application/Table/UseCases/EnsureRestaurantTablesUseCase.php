<?php

namespace App\Application\Table\UseCases;

use App\Domain\Table\Repositories\RestaurantTableRepositoryInterface;

final readonly class EnsureRestaurantTablesUseCase
{
    public function __construct(private RestaurantTableRepositoryInterface $tables)
    {
    }

    public function execute(): void
    {
        $this->tables->ensureRange(1, 50);
    }
}
