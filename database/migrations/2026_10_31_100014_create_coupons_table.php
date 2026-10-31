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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 12, 2);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->enum('target_audience', ['all', 'member_only', 'non_member_only'])->default('all');
            $table->decimal('min_purchase', 12, 2)->default(0);
            $table->integer('min_quantity')->default(0);
            $table->integer('quota_total')->nullable();
            $table->integer('quota_per_user')->nullable();
            $table->enum('restriction_type', ['none', 'category', 'brand', 'product'])->default('none');
            $table->boolean('show_on_checkout')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
