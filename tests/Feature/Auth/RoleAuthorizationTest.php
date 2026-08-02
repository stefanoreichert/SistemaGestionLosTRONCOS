<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::connection()->getPdo()->sqliteCreateFunction(
            'HOUR',
            static fn (?string $value): int => $value === null ? 0 : (int) date('G', strtotime($value)),
        );
    }

    public function test_mozo_can_access_tables_list_and_detail(): void
    {
        $mozo = $this->mozo();

        $this->actingAs($mozo)->get(route('tables.index'))->assertOk();
        $this->get(route('tables.show', 1))->assertOk();
    }

    public function test_mozo_cannot_access_administrative_modules(): void
    {
        $this->actingAs($this->mozo());

        foreach ([
            route('dashboard'),
            route('products.index'),
            route('users.index'),
            route('reports.daily'),
            route('reports.monthly'),
            route('reports.daily-sales'),
            route('reports.sold-products'),
            route('tickets.index'),
        ] as $uri) {
            $this->get($uri)->assertForbidden();
        }

    }

    public function test_admin_keeps_access_to_every_existing_module(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        foreach ([
            route('dashboard'),
            route('products.index'),
            route('users.index'),
            route('tables.index'),
            route('reports.daily'),
            route('reports.monthly'),
            route('reports.daily-sales'),
            route('reports.sold-products'),
            route('tickets.index'),
        ] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_caja_keeps_its_current_access(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_CAJA]));

        foreach ([
            route('dashboard'),
            route('products.index'),
            route('tables.index'),
            route('reports.daily'),
            route('reports.monthly'),
            route('reports.daily-sales'),
            route('reports.sold-products'),
            route('tickets.index'),
        ] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_mozo_menu_only_contains_tables_password_change_and_logout(): void
    {
        $response = $this->actingAs($this->mozo())->get(route('tables.index'))->assertOk();

        $response
            ->assertSee(route('tables.index'), false)
            ->assertSee(route('password.edit'), false)
            ->assertSee(route('logout'), false)
            ->assertDontSee('Panel principal')
            ->assertDontSee(route('products.index'), false)
            ->assertDontSee(route('users.index'), false)
            ->assertDontSee(route('reports.daily'), false)
            ->assertDontSee(route('reports.monthly'), false)
            ->assertDontSee(route('tickets.index'), false);
    }

    public function test_mozo_is_redirected_to_tables_after_login(): void
    {
        $mozo = $this->mozo();

        $this->withSession(['url.intended' => route('dashboard')])
            ->post(route('login.store'), [
                'email' => $mozo->email,
                'password' => 'password',
            ])->assertRedirect(route('tables.index'));
    }

    public function test_mozo_can_change_password_and_log_out(): void
    {
        $this->actingAs($this->mozo())
            ->get(route('password.edit'))
            ->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    private function mozo(): User
    {
        $waiter = WaiterModel::query()->create([
            'name' => 'Mozo de prueba',
            'is_active' => true,
        ]);

        return User::factory()->waiter((int) $waiter->id)->create();
    }
}
