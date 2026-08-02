<?php

namespace Tests\Feature;

use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class WaiterManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_waiters_remain_available_as_internal_operational_entities(): void
    {
        $this->waiter('Ana Activa', true, 'M-001', '0981111111');
        $this->waiter('Bruno Inactivo', false, 'M-002', '0982222222');
        $repository = app(WaiterRepositoryInterface::class);

        $this->assertCount(2, $repository->all());
        $this->assertCount(1, $repository->active());
        $this->assertSame('Ana Activa', $repository->active()[0]->name());
    }

    public function test_waiter_management_routes_from_main_are_preserved(): void
    {
        $this->assertTrue(Route::has('waiters.index'));
        $this->assertTrue(Route::has('waiters.store'));
        $this->assertTrue(Route::has('waiters.update'));
        $this->assertTrue(Route::has('waiters.availability'));
    }

    private function waiter(
        string $name,
        bool $isActive,
        ?string $employeeCode = null,
        ?string $phone = null,
    ): WaiterModel {
        return WaiterModel::query()->create([
            'name' => $name,
            'employee_code' => $employeeCode,
            'phone' => $phone,
            'is_active' => $isActive,
        ]);
    }
}
