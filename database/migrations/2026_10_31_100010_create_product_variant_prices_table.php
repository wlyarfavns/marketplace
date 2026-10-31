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
        Schema::create('product_variant_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_model_id')->constrained('product_models')->onDelete('cascade');
            $table->foreignId('product_size_id')->constrained('product_sizes')->onDelete('cascade');
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'product_model_id', 'product_size_id'], 'variant_price_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_prices');
    }
};
