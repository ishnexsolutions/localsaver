<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->boolean('vip_unlocked')->default(false);
            $table->boolean('referrer_reward_claimed')->default(false);
            $table->boolean('referred_reward_claimed')->default(false);
            $table->unsignedInteger('referred_redemption_count')->default(0);
            $table->timestamps();

            $table->unique('referred_id');
            $table->index('referrer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
