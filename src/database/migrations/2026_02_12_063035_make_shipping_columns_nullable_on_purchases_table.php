<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // doctrine/dbal を避けるため drop & add
            $table->dropColumn(['postal_code', 'address', 'building_name']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('building_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'address', 'building_name']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            // 戻す（NOT NULL）
            $table->string('postal_code');
            $table->string('address');
            $table->string('building_name');
        });
    }
};
