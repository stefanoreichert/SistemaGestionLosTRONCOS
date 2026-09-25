<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_closures', function (Blueprint $table): void {
            $table->id();
            $table->dateTime('period_started_at');
            $table->dateTime('closed_at')->index();
            $table->foreignId('closed_by_user_id')->constrained('users')->restrictOnDelete();
            $table->uuid('idempotency_key')->unique();
            $table->json('report_snapshot');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('daily_report_closure_id')
                ->nullable()
                ->after('ticket_number')
                ->constrained('daily_report_closures')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('daily_report_closure_id');
        });

        Schema::dropIfExists('daily_report_closures');
    }
};
