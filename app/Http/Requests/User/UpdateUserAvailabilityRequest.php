<?php

namespace App\Http\Requests\User;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class UpdateUserAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = User::query()->find($this->route('user'));

        return $target !== null && Gate::forUser($this->user())->allows('setAvailability', $target);
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return ['is_active' => ['required', 'boolean']];
    }
}
