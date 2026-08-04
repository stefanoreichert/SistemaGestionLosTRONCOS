<?php

namespace App\Http\Requests\User;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;

final class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = User::query()->find($this->route('user'));

        return $target !== null && Gate::forUser($this->user())->allows('changePassword', $target);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::defaults()]];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
