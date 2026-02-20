<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->index('expiry_date');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->index(['lat', 'lng']);
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->index(['user_id', 'coupon_id']);
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['expiry_date']);
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['lat', 'lng']);
        });
        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'coupon_id']);
        });
    }
};
