<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class WaiterModel extends Model
{
    protected $table = 'waiters';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'employee_code',
        'phone',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
