<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('table_consumption_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('table_consumption_id')->constrained('table_consumptions')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price_in_cents');
            $table->timestamps();
            $table->unique(['table_consumption_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_consumption_items');
    }
};
