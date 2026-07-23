<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TableModel extends Model
{
    protected $table = 'tables';

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'number',
    ];

    public function openOrder(): HasOne
    {
        return $this->hasOne(OrderModel::class, 'table_id')->where('status', 'open');
    }
}
