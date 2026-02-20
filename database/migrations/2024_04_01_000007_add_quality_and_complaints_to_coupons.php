<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('quality_score', 5, 2)->nullable()->after('priority_score');
            $table->unsignedInteger('complaint_count')->default(0)->after('quality_score');
            $table->unsignedInteger('rating_sum')->default(0)->after('complaint_count');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_sum');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['quality_score', 'complaint_count', 'rating_sum', 'rating_count']);
        });
    }
};
