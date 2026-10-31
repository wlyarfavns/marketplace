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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('seller_id')->nullable()->constrained('sellers')->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');

            // Recipient information
            $table->string('recipient_first_name');
            $table->string('recipient_last_name');
            $table->string('whatsapp', 20);
            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 10);

            // Shipping
            $table->string('courier_name', 50)->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0);

            // Totals
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);

            // Payment
            $table->enum('payment_method_type', ['dp', 'full'])->default('full');
            $table->decimal('dp_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);

            // Statuses
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'waiting_verification', 'verified', 'rejected'])->default('unpaid');
            $table->enum('dp_status', ['none', 'dp_paid', 'fully_paid'])->default('none');

            // Notes
            $table->text('customer_notes')->nullable();

            // Different shipping address
            $table->boolean('different_shipping_address')->default(false);
            $table->string('shipping_first_name')->nullable();
            $table->string('shipping_last_name')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_province')->nullable();
            $table->string('shipping_postal_code', 10)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
