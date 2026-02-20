<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_daily', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('dau')->default(0);
            $table->unsignedInteger('wau')->default(0);
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('total_businesses')->default(0);
            $table->unsignedInteger('total_coupons')->default(0);
            $table->unsignedInteger('redemptions_count')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_daily');
    }
};
