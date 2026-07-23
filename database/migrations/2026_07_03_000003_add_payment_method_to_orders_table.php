<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 30)->nullable()->after('total');
                $table->index('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropIndex(['payment_method']);
                $table->dropColumn('payment_method');
            }
        });
    }
};
