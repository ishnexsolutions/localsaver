<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('discount_value', 10, 2);
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->unsignedInteger('max_redemptions');
            $table->unsignedInteger('used_count')->default(0);
            $table->date('expiry_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('radius_km', 5, 2)->default(5);
            $table->boolean('first_time_only')->default(false);
            $table->boolean('is_boosted')->default(false);
            $table->timestamp('boosted_until')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->enum('status', ['active', 'expired', 'rejected'])->default('active');
            $table->timestamps();

            $table->index(['expiry_date', 'status']);
            $table->index('is_boosted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
