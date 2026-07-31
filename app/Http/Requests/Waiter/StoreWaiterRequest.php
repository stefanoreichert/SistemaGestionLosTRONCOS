<?php

namespace App\Http\Requests\Waiter;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaiterRequest extends FormRequest
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
            'employee_code' => ['nullable', 'string', 'max:50', 'unique:waiters,employee_code'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
