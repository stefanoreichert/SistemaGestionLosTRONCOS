<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_change_their_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-anterior')]);

        $this->actingAs($user)->patch(route('password.update'), [
            'current_password' => 'password-anterior',
            'password' => 'password-nueva',
            'password_confirmation' => 'password-nueva',
        ])->assertSessionHas('status', 'Contraseña actualizada correctamente.');

        $this->assertTrue(Hash::check('password-nueva', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_the_current_password_is_required_to_change_it(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-anterior')]);

        $this->actingAs($user)->patch(route('password.update'), [
            'current_password' => 'incorrecta',
            'password' => 'password-nueva',
            'password_confirmation' => 'password-nueva',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-anterior', $user->fresh()->password));
    }
}
