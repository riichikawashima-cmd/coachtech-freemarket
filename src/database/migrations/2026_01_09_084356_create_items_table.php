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
            $table->string('name');
            $table->string('brand')->nullable();
            $table->text('description');
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('condition');
            $table->string('image_path');

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
