<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_available_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Ingresar')
            ->assertSee('Recordarme');
    }

    public function test_a_user_can_log_in_with_email_and_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-segura')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password-segura',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_remember_me_creates_a_recaller_cookie(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-segura')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password-segura',
            'remember' => true,
        ])->assertCookie(Auth::guard('web')->getRecallerName());
    }

    public function test_invalid_credentials_return_a_generic_error(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'incorrecta',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Las credenciales ingresadas no son válidas.',
            ]);

        $this->assertGuest();
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), [
                'email' => 'admin@example.com',
                'password' => 'incorrecta',
            ]);
        }

        $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors([
            'email' => 'Demasiados intentos. Intente nuevamente más tarde.',
        ]);
    }

    public function test_an_authenticated_user_can_log_out_only_with_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get('/logout')->assertMethodNotAllowed();
    }

    public function test_the_initial_user_command_creates_a_hashed_account(): void
    {
        $this->artisan('user:create')
            ->expectsQuestion('Nombre', 'Administrador')
            ->expectsQuestion('Correo electrónico', 'admin@example.com')
            ->expectsQuestion('Contraseña', 'password-segura')
            ->expectsQuestion('Confirmar contraseña', 'password-segura')
            ->expectsOutput('Usuario creado correctamente.')
            ->assertSuccessful();

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertNotSame('password-segura', $user->password);
        $this->assertTrue(Hash::check('password-segura', $user->password));
    }
}
