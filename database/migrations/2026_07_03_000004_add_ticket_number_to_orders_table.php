<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (!Schema::hasColumn('orders', 'ticket_number')) {
                $table->string('ticket_number', 20)->nullable()->after('payment_method');
                $table->unique('ticket_number');
            }
        });

        DB::table('orders')
            ->where('status', 'closed')
            ->whereNull('ticket_number')
            ->orderBy('closed_at')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $order, int $index): void {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['ticket_number' => str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT)]);
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'ticket_number')) {
                $table->dropUnique(['ticket_number']);
                $table->dropColumn('ticket_number');
            }
        });
    }
};
