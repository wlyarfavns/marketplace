<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name_snapshot');
            $table->string('model_name');
            $table->string('color_name');
            $table->string('size_name');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('rule_adjustment', 12, 2)->default(0);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->text('bonus_item_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
