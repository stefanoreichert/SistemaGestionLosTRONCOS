<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function orders(): HasMany
    {
        return $this->hasMany(OrderModel::class, 'waiter_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'waiter_id');
    }
}
