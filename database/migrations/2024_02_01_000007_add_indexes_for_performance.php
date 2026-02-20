<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->index('boosted_until');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['boosted_until']);
        });
        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });
    }
};
