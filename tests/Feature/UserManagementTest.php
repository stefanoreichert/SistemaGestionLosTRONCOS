<?php

namespace Tests\Feature;

use App\Application\User\Services\UserManagementService;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Models\WaiterModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_list_and_search_all_users(): void
    {
        User::factory()->create(['name' => 'Usuario Caja', 'phone' => '0981111111', 'email' => 'caja@example.com', 'role' => User::ROLE_CAJA]);

        $this->get(route('users.index', ['search' => '0981111111']))
            ->assertOk()->assertSee('Usuario Caja')->assertSee('CAJA');
    }

    public function test_admin_can_create_every_supported_role_and_passwords_are_hashed(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_CAJA, User::ROLE_MOZO, User::ROLE_KITCHEN] as $index => $role) {
            $email = "usuario{$index}@example.com";
            $this->post(route('users.store'), [
                'name' => 'Usuario '.$role,
                'phone' => '098100000'.$index,
                'email' => $email,
                'password' => 'password-segura',
                'password_confirmation' => 'password-segura',
                'role' => $role,
            ])->assertRedirect(route('users.index'));

            $user = User::query()->where('email', $email)->firstOrFail();
            $this->assertTrue(Hash::check('password-segura', $user->password));
            $this->assertSame($role === User::ROLE_MOZO, $user->waiter_id !== null);
        }
    }

    public function test_creating_a_waiter_user_creates_an_exclusive_synchronized_profile(): void
    {
        $this->post(route('users.store'), [
            'name' => 'Mozo Operativo', 'phone' => '0981222333', 'email' => 'mozo@example.com',
            'password' => 'password-segura', 'password_confirmation' => 'password-segura', 'role' => User::ROLE_MOZO,
            'waiter_id' => 999999,
        ])->assertRedirect(route('users.index'));

        $user = User::query()->where('email', 'mozo@example.com')->firstOrFail();
        $waiter = $user->waiter()->firstOrFail();
        $this->assertNotSame(999999, (int) $user->waiter_id);
        $this->assertSame('Mozo Operativo', $waiter->name);
        $this->assertSame('0981222333', $waiter->phone);
        $this->assertTrue($waiter->is_active);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['role' => User::ROLE_MOZO, 'waiter_id' => $waiter->id]);
    }

    public function test_admin_can_edit_identity_phone_email_and_role(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CAJA]);
        $this->put(route('users.update', $user), [
            'name' => 'Nombre Editado', 'phone' => '0971000000', 'email' => 'editado@example.com', 'role' => User::ROLE_KITCHEN,
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id, 'name' => 'Nombre Editado', 'phone' => '0971000000',
            'email' => 'editado@example.com', 'role' => User::ROLE_KITCHEN, 'waiter_id' => null,
        ]);
    }

    public function test_leaving_waiter_role_detaches_and_deactivates_profile_without_deleting_it(): void
    {
        $waiter = WaiterModel::query()->create(['name' => 'Histórico', 'is_active' => true]);
        $user = User::factory()->waiter((int) $waiter->id)->create();

        $this->put(route('users.update', $user), [
            'name' => 'Ahora Caja', 'phone' => '', 'email' => $user->email, 'role' => User::ROLE_CAJA,
        ])->assertRedirect(route('users.index'));

        $this->assertNull($user->fresh()->waiter_id);
        $this->assertDatabaseHas('waiters', ['id' => $waiter->id, 'is_active' => false]);
    }

    public function test_admin_can_activate_and_deactivate_another_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CAJA]);
        $this->patch(route('users.availability', $user), ['is_active' => false])->assertRedirect();
        $this->assertFalse($user->fresh()->is_active);
        $this->patch(route('users.availability', $user), ['is_active' => true])->assertRedirect();
        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_self_or_remove_own_admin_role(): void
    {
        $this->patch(route('users.availability', $this->admin), ['is_active' => false])->assertSessionHasErrors('is_active');
        $this->put(route('users.update', $this->admin), [
            'name' => $this->admin->name, 'phone' => '', 'email' => $this->admin->email, 'role' => User::ROLE_CAJA,
        ])->assertSessionHasErrors('role');
        $this->assertTrue($this->admin->fresh()->is_active);
        $this->assertTrue($this->admin->fresh()->isAdmin());
    }

    public function test_service_cannot_deactivate_or_demote_the_last_active_admin(): void
    {
        $service = app(UserManagementService::class);
        $this->expectException(ValidationException::class);
        $service->setAvailability((int) $this->admin->id, false, 999999);
    }

    public function test_admin_can_change_password_with_separate_action(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CAJA]);
        $this->patch(route('users.password', $user), [
            'password' => 'password-nueva', 'password_confirmation' => 'password-nueva',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('password-nueva', $user->fresh()->password));
    }

    public function test_email_must_be_unique_and_phone_is_optional(): void
    {
        $existing = User::factory()->create();
        $this->post(route('users.store'), [
            'name' => 'Duplicado', 'phone' => '', 'email' => $existing->email,
            'password' => 'password-segura', 'password_confirmation' => 'password-segura', 'role' => User::ROLE_CAJA,
        ])->assertSessionHasErrors('email');
    }

    public function test_non_admin_cannot_manage_users_and_destroy_route_does_not_exist(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CAJA]);
        $this->actingAs($cashier)->get(route('users.index'))->assertForbidden();
        $this->assertFalse(Route::has('users.destroy'));
        $this->delete('/users/'.$cashier->id)->assertMethodNotAllowed();
        $this->assertDatabaseHas('users', ['id' => $cashier->id]);
    }

    public function test_admin_navigation_shows_users_and_hides_waiters(): void
    {
        $this->get(route('users.index'))->assertOk()
            ->assertSee(route('users.index'), false)
            ->assertDontSee('Mozos');
    }
}
