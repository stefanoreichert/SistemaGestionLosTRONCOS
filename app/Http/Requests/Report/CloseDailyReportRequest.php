<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

final class CloseDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'uuid'],
        ];
    }
}
