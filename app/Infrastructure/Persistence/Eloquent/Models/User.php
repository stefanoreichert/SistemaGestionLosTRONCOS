<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'ADMIN';

    public const ROLE_MOZO = 'MOZO';

    public const ROLE_CAJA = 'CAJA';

    public const ROLE_KITCHEN = 'COCINA';

    public const ROLE_WAITER = self::ROLE_MOZO;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'waiter_id',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'waiter_id' => 'integer',
        ];
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(WaiterModel::class, 'waiter_id');
    }

    public function isWaiter(): bool
    {
        return $this->role === self::ROLE_MOZO;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKitchen(): bool
    {
        return $this->role === self::ROLE_KITCHEN;
    }
}
