<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            // いいねしたユーザー
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // いいねされた商品
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // 同一ユーザーが同一商品に複数いいねできないようにする
            $table->unique(['user_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
