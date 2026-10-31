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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'customer'])->default('customer')->after('name');
            $table->enum('customer_type', ['umum', 'member', 'distributor'])->default('umum')->after('role');
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('province', 100)->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
            $table->string('default_seller_code', 50)->nullable()->after('postal_code');
            $table->string('favorite_courier', 50)->nullable()->after('default_seller_code');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('favorite_courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'customer_type',
                'phone',
                'address',
                'city',
                'province',
                'postal_code',
                'default_seller_code',
                'favorite_courier',
                'status',
            ]);
        });
    }
};
