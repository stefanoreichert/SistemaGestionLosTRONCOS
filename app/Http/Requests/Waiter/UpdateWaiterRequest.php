<?php

namespace App\Http\Requests\Waiter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWaiterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'employee_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('waiters', 'employee_code')->ignore($this->route('waiter')),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
