<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_operational_routes(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->get(route('users.index'))->assertRedirect(route('login'));
        $this->get(route('tables.index'))->assertRedirect(route('login'));
        $this->get(route('tickets.index'))->assertRedirect(route('login'));
        $this->get(route('reports.daily'))->assertRedirect(route('login'));
    }

    public function test_every_operational_route_has_auth_middleware(): void
    {
        $protectedRouteNames = ['dashboard', 'password.edit', 'password.update', 'logout'];
        $protectedRoutePrefixes = ['products.', 'users.', 'tables.', 'reports.', 'tickets.'];
        $auditedRoutes = 0;

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            if ($name === null
                || (! in_array($name, $protectedRouteNames, true)
                    && ! collect($protectedRoutePrefixes)->contains(
                        fn (string $prefix): bool => str_starts_with($name, $prefix),
                    ))) {
                continue;
            }

            $this->assertContains('auth', $route->gatherMiddleware(), "La ruta {$name} debe requerir auth.");
            $auditedRoutes++;
        }

        $this->assertSame(32, $auditedRoutes);
    }

    public function test_authenticated_users_can_access_the_dashboard_and_guests_cannot_access_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->actingAs($user)->get(route('login'))->assertRedirect(route('dashboard'));
    }
}
