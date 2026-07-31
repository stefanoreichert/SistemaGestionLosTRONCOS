<?php

namespace Tests\Feature;

use App\Domain\Waiter\Repositories\WaiterRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaiterManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_the_list_includes_active_and_inactive_waiters(): void
    {
        $this->waiter('Ana Activa');
        $this->waiter('Bruno Inactivo', false);

        $this->get(route('waiters.index'))
            ->assertOk()
            ->assertSee('Ana Activa')
            ->assertSee('Bruno Inactivo')
            ->assertSee('Activo')
            ->assertSee('Inactivo');
    }

    public function test_waiters_can_be_searched_by_name_code_or_phone(): void
    {
        $this->waiter('Ana Gómez', true, 'M-001', '0981111111');
        $this->waiter('Bruno López', false, 'M-002', '0982222222');

        $this->get(route('waiters.index', ['search' => 'M-002']))
            ->assertOk()
            ->assertSee('Bruno López')
            ->assertDontSee('Ana Gómez');

        $this->get(route('waiters.index', ['search' => '0981111111']))
            ->assertOk()
            ->assertSee('Ana Gómez')
            ->assertDontSee('Bruno López');
    }

    public function test_a_waiter_can_be_created(): void
    {
        $this->post(route('waiters.store'), [
            'name' => 'Ana Gómez',
            'employee_code' => 'M-001',
            'phone' => '0981111111',
        ])->assertRedirect(route('waiters.index'))
            ->assertSessionHas('status', 'Mozo creado correctamente.');

        $this->assertDatabaseHas('waiters', [
            'name' => 'Ana Gómez',
            'employee_code' => 'M-001',
            'phone' => '0981111111',
            'is_active' => true,
        ]);
    }

    public function test_a_waiter_can_be_updated_without_changing_availability(): void
    {
        $waiter = $this->waiter('Nombre anterior', false, 'M-001');

        $this->put(route('waiters.update', $waiter), [
            'name' => 'Nombre actualizado',
            'employee_code' => 'M-001',
            'phone' => '0981000000',
        ])->assertRedirect(route('waiters.index'))
            ->assertSessionHas('status', 'Mozo actualizado correctamente.');

        $this->assertDatabaseHas('waiters', [
            'id' => $waiter->id,
            'name' => 'Nombre actualizado',
            'is_active' => false,
        ]);
    }

    public function test_name_is_required_and_employee_code_must_be_unique(): void
    {
        $this->waiter('Ana', true, 'M-001');

        $this->post(route('waiters.store'), [
            'name' => '',
            'employee_code' => 'M-001',
        ])->assertSessionHasErrors(['name', 'employee_code']);
    }

    public function test_a_waiter_can_be_deactivated_without_being_deleted_and_reactivated(): void
    {
        $waiter = $this->waiter('Ana');

        $this->patch(route('waiters.availability', $waiter), ['is_active' => false])
            ->assertRedirect(route('waiters.index'))
            ->assertSessionHas('success', 'Mozo desactivado correctamente.');

        $this->assertDatabaseHas('waiters', ['id' => $waiter->id, 'is_active' => false]);
        $this->assertSame(1, WaiterModel::query()->count());

        $this->patch(route('waiters.availability', $waiter), ['is_active' => true])
            ->assertRedirect(route('waiters.index'))
            ->assertSessionHas('success', 'Mozo activado correctamente.');

        $this->assertDatabaseHas('waiters', ['id' => $waiter->id, 'is_active' => true]);
    }

    public function test_the_active_repository_method_excludes_inactive_waiters_only_when_requested(): void
    {
        $this->waiter('Ana Activa');
        $this->waiter('Bruno Inactivo', false);

        $repository = app(WaiterRepositoryInterface::class);

        $this->assertCount(2, $repository->all());
        $this->assertCount(1, $repository->active());
        $this->assertSame('Ana Activa', $repository->active()[0]->name());
    }

    public function test_the_waiter_destroy_route_does_not_exist(): void
    {
        $waiter = $this->waiter('Ana');

        $this->delete('/waiters/'.$waiter->id)->assertMethodNotAllowed();
        $this->assertDatabaseHas('waiters', ['id' => $waiter->id]);
    }

    public function test_unknown_waiters_return_not_found(): void
    {
        $this->get(route('waiters.edit', 999999))->assertNotFound();
        $this->patch(route('waiters.availability', 999999), ['is_active' => false])->assertNotFound();
    }

    private function waiter(
        string $name,
        bool $isActive = true,
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
