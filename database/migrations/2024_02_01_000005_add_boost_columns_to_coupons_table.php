<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->boolean('featured_flag')->default(false)->after('is_boosted');
            $table->unsignedInteger('priority_score')->default(0)->after('featured_flag');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['featured_flag', 'priority_score']);
        });
    }
};
