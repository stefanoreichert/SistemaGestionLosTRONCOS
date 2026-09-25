<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class DailyReportClosureModel extends Model
{
    protected $table = 'daily_report_closures';

    /** @var list<string> */
    protected $fillable = [
        'period_started_at',
        'closed_at',
        'closed_by_user_id',
        'idempotency_key',
        'report_snapshot',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'period_started_at' => 'datetime',
            'closed_at' => 'datetime',
            'closed_by_user_id' => 'integer',
            'report_snapshot' => 'array',
        ];
    }
}
