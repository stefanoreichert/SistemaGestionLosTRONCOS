<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderModel extends Model
{
    protected $table = 'orders';

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'table_id',
        'waiter_id',
        'status',
        'kitchen_status',
        'subtotal',
        'total',
        'payment_method',
        'ticket_number',
        'opened_at',
        'closed_at',
        'sent_to_kitchen_at',
        'kitchen_started_at',
        'kitchen_ready_at',
        'kitchen_retired_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'waiter_id' => 'integer',
            'sent_to_kitchen_at' => 'datetime',
            'kitchen_started_at' => 'datetime',
            'kitchen_ready_at' => 'datetime',
            'kitchen_retired_at' => 'datetime',
        ];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(TableModel::class, 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(WaiterModel::class, 'waiter_id');
    }
}
