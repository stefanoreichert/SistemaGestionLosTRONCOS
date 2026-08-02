<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->boolean('requires_kitchen')->default(true)->index();
        });
        Schema::table('order_items', function (Blueprint $table): void {
            $table->boolean('requires_kitchen')->default(true)->index();
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('kitchen_status', 30)->nullable()->after('status');
            $table->timestamp('sent_to_kitchen_at')->nullable()->after('opened_at');
            $table->timestamp('kitchen_started_at')->nullable()->after('sent_to_kitchen_at');
            $table->timestamp('kitchen_ready_at')->nullable()->after('kitchen_started_at');
            $table->timestamp('kitchen_retired_at')->nullable()->after('kitchen_ready_at');
            $table->index(['status', 'kitchen_status', 'sent_to_kitchen_at'], 'orders_active_kitchen_queue_index');
        });

        $beverageIds = DB::table('products')
            ->whereIn('category', ['Cervezas', 'Sin alcohol', 'Tragos', 'Vinos'])
            ->pluck('id');
        DB::table('products')->whereIn('id', $beverageIds)->update(['requires_kitchen' => false]);
        DB::table('order_items')->whereIn('product_id', $beverageIds)->update(['requires_kitchen' => false]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex('orders_active_kitchen_queue_index');
            $table->dropColumn(['kitchen_status', 'sent_to_kitchen_at', 'kitchen_started_at', 'kitchen_ready_at', 'kitchen_retired_at']);
        });
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('requires_kitchen'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('requires_kitchen'));
    }
};
