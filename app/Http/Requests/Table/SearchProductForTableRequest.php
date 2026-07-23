<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductForTableRequest extends FormRequest
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
            'product_name' => ['required', 'string', 'max:150'],
        ];
    }
}
