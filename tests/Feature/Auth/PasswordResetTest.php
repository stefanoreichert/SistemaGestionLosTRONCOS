<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_reset_link_can_be_requested_without_revealing_if_the_account_exists(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $message = 'Si el correo está registrado, recibirá un enlace para restablecer la contraseña.';

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status', $message);
        $this->post(route('password.email'), ['email' => 'inexistente@example.com'])
            ->assertSessionHas('status', $message);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_a_password_can_be_reset_with_a_valid_token(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $token = null;

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function (ResetPassword $notification) use (&$token): bool {
                $token = $notification->token;

                return true;
            },
        );

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'nueva-password',
            'password_confirmation' => 'nueva-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Contraseña restablecida correctamente.');

        $this->assertTrue(Hash::check('nueva-password', $user->fresh()->password));
    }

    public function test_an_invalid_reset_token_returns_a_generic_error(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.store'), [
            'token' => 'token-invalido',
            'email' => $user->email,
            'password' => 'nueva-password',
            'password_confirmation' => 'nueva-password',
        ])->assertSessionHasErrors([
            'email' => 'No se pudo restablecer la contraseña. Solicite un nuevo enlace.',
        ]);
    }
}
