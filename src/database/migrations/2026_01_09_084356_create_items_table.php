<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 出品者
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 商品情報（出品画面に対応）
            $table->string('name');                 // 商品名
            $table->string('brand')->nullable();              // ブランド名（画面にあるので保持、未入力OK）
            $table->text('description');            // 商品説明
            $table->unsignedInteger('price');       // 価格
            $table->unsignedTinyInteger('condition');            // 状態（stringでOK）
            $table->string('image_path');           // 画像パス

            $table->timestamps();

            // よく検索/並び替えで使うのでインデックス（任意だけど入れとくと強い）
            $table->index('user_id');
            $table->index('price');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
