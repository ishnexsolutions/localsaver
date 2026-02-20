<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_active_at')->nullable()->after('redemption_count');
            $table->unsignedInteger('redemption_streak_weeks')->default(0)->after('last_active_at');
            $table->boolean('vip_unlocked')->default(false)->after('referred_by');
            $table->unsignedInteger('referral_count')->default(0)->after('vip_unlocked');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_active_at', 'redemption_streak_weeks', 'vip_unlocked', 'referral_count']);
        });
    }
};
