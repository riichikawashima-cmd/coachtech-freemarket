<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            // 購入者
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 購入された商品（1商品1購入）
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            // 支払い方法（例：credit_card / convenience_store）
            $table->string('payment_method');

            $table->timestamps();

            // 検索・集計用
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
