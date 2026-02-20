<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('referral_count');
            $table->json('preferred_categories')->nullable()->after('onboarding_completed');
            $table->boolean('founding_member')->default(false)->after('preferred_categories');
            $table->unsignedInteger('loyalty_streak_days')->default(0)->after('founding_member');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed', 'preferred_categories', 'founding_member', 'loyalty_streak_days']);
        });
    }
};
