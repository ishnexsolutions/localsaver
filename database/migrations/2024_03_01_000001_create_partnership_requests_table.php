<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnership_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('target_business_id')->constrained('businesses')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['requester_business_id', 'target_business_id'], 'part_req_requester_target_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partnership_requests');
    }
};
