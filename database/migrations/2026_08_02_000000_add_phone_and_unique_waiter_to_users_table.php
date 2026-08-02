<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasUniqueWaiterId = collect(Schema::getIndexes('users'))->contains(
            static fn (array $index): bool => $index['unique'] === true
                && $index['columns'] === ['waiter_id'],
        );

        $duplicatedWaiter = DB::table('users')->select('waiter_id')
            ->whereNotNull('waiter_id')->groupBy('waiter_id')
            ->havingRaw('COUNT(*) > 1')->exists();

        if ($duplicatedWaiter) {
            throw new RuntimeException('No se puede crear la relación uno a uno: existen usuarios con waiter_id duplicado.');
        }

        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('phone', 30)->nullable()->after('name');
            });
        }

        if (! $hasUniqueWaiterId) {
            Schema::table('users', function (Blueprint $table): void {
                $table->unique('waiter_id');
            });
        }
    }

    public function down(): void
    {
        $hasUniqueWaiterId = collect(Schema::getIndexes('users'))->contains(
            static fn (array $index): bool => $index['name'] === 'users_waiter_id_unique',
        );

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });

        if ($hasUniqueWaiterId) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique('users_waiter_id_unique');
            });
        }
    }
};
