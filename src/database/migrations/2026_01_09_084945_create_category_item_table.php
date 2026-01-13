<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_item', function (Blueprint $table) {
            // items × categories の中間
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            // 同じ組み合わせの重複防止
            $table->primary(['item_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_item');
    }
};
