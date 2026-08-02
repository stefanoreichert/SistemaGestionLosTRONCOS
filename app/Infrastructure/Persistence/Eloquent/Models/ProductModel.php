<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'products';

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'price',
        'is_active',
        'requires_kitchen',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_kitchen' => 'boolean',
        ];
    }
}
