<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // コメントしたユーザー
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // コメント対象の商品
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            // コメント本文
            $table->text('comment');

            $table->timestamps();

            // よく使う条件にインデックス（任意だけどおすすめ）
            $table->index('item_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
