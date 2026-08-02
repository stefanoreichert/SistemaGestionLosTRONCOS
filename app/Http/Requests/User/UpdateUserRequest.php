<?php

namespace App\Http\Requests\User;

use App\Domain\User\Enums\UserRole;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = User::query()->find($this->route('user'));

        return $target !== null && Gate::forUser($this->user())->allows('update', $target);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'role' => ['required', Rule::enum(UserRole::class)],
        ];
    }
}
