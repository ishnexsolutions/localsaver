<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('daily_deals')->default(true);
            $table->boolean('flash_deals')->default(true);
            $table->boolean('milestones')->default(true);
            $table->boolean('comeback')->default(true);
            $table->time('silent_start')->nullable(); // e.g. 22:00
            $table->time('silent_end')->nullable();   // e.g. 08:00
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
