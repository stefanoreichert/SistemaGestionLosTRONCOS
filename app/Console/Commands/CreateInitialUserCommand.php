<?php

namespace App\Console\Commands;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateInitialUserCommand extends Command
{
    protected $signature = 'user:create';

    protected $description = 'Crea una cuenta inicial para acceder al sistema';

    public function handle(): int
    {
        $name = (string) $this->ask('Nombre');
        $email = (string) $this->ask('Correo electrónico');
        $password = (string) $this->secret('Contraseña');
        $passwordConfirmation = (string) $this->secret('Confirmar contraseña');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::query()->create([
            'name' => trim($name),
            'email' => mb_strtolower(trim($email)),
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->info('Usuario creado correctamente.');

        return self::SUCCESS;
    }
}
