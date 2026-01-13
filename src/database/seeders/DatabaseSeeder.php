<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // users
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                'name' => 'testuser',
                'email' => 'test@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // conditions
        if (DB::table('conditions')->count() === 0) {
            DB::table('conditions')->insert([
                ['name' => '良好', 'created_at' => now(), 'updated_at' => now()],
                ['name' => '目立った傷や汚れなし', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'やや傷や汚れあり', 'created_at' => now(), 'updated_at' => now()],
                ['name' => '状態が悪い', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // categories（画像どおり）
        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['name' => 'ファッション', 'created_at' => now(), 'updated_at' => now()],
                ['name' => '家電', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'インテリア', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'レディース', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'メンズ', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'コスメ', 'created_at' => now(), 'updated_at' => now()],
                ['name' => '本', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'ゲーム', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'スポーツ', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'キッチン', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'ハンドメイド', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'アクセサリー', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'おもちゃ', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'ベビー・キッズ', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // items & category_item
        if (DB::table('items')->count() === 0) {
            $userId = DB::table('users')->value('id');

            $items = [
                ['name' => '腕時計', 'price' => 15000, 'brand' => 'Rolax', 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'image_path' => 'images/items/watch.jpg', 'condition' => 1],
                ['name' => 'HDD', 'price' => 5000, 'brand' => '西芝', 'description' => '高速で信頼性の高いハードディスク', 'image_path' => 'images/items/hdd.jpg', 'condition' => 2],
                ['name' => '玉ねぎ3束', 'price' => 300, 'brand' => 'なし', 'description' => '新鮮な玉ねぎ3束のセット', 'image_path' => 'images/items/onion-set.jpg', 'condition' => 3],
                ['name' => '革靴', 'price' => 4000, 'brand' => '', 'description' => 'クラシックなデザインの革靴', 'image_path' => 'images/items/leather-shoes.jpg', 'condition' => 4],
                ['name' => 'ノートPC', 'price' => 45000, 'brand' => '', 'description' => '高性能なノートパソコン', 'image_path' => 'images/items/laptop.jpg', 'condition' => 1],
                ['name' => 'マイク', 'price' => 8000, 'brand' => 'なし', 'description' => '高音質のレコーディング用マイク', 'image_path' => 'images/items/microphone.jpg', 'condition' => 2],
                ['name' => 'ショルダーバッグ', 'price' => 3500, 'brand' => '', 'description' => 'おしゃれなショルダーバッグ', 'image_path' => 'images/items/shoulder-bag.jpg', 'condition' => 3],
                ['name' => 'タンブラー', 'price' => 500, 'brand' => 'なし', 'description' => '使いやすいタンブラー', 'image_path' => 'images/items/tumbler.jpg', 'condition' => 4],
                ['name' => 'コーヒーミル', 'price' => 4000, 'brand' => 'Starbacks', 'description' => '手動のコーヒーミル', 'image_path' => 'images/items/coffee-mill.jpg', 'condition' => 1],
                ['name' => 'メイクセット', 'price' => 2500, 'brand' => '', 'description' => '便利なメイクアップセット', 'image_path' => 'images/items/makeup-set.jpg', 'condition' => 2],
            ];

            // categories name => id
            $cat = DB::table('categories')->pluck('id', 'name')->toArray();

            // itemsを入れて、入ったidを順番に保持
            $insertedItemIds = [];
            foreach ($items as $item) {
                $insertedItemIds[] = DB::table('items')->insertGetId([
                    'user_id' => $userId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'brand' => $item['brand'],
                    'description' => $item['description'],
                    'image_path' => $item['image_path'],
                    'condition' => $item['condition'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 10件分にカテゴリを割り当て
            $map = [
                0 => ['メンズ', 'アクセサリー'],     // 腕時計
                1 => ['家電'],                       // HDD
                2 => ['キッチン'],                   // 玉ねぎ
                3 => ['メンズ', 'ファッション'],     // 革靴
                4 => ['家電'],                       // ノートPC
                5 => ['家電'],                       // マイク
                6 => ['レディース', 'ファッション'], // バッグ
                7 => ['キッチン'],                   // タンブラー
                8 => ['キッチン'],                   // コーヒーミル
                9 => ['レディース', 'コスメ'],       // メイクセット
            ];

            foreach ($map as $index => $names) {
                $itemId = $insertedItemIds[$index] ?? null;
                if (!$itemId) continue;

                foreach ($names as $name) {
                    if (!isset($cat[$name])) continue;

                    DB::table('category_item')->insert([
                        'item_id' => $itemId,
                        'category_id' => $cat[$name],
                    ]);
                }
            }
        }
    }
}
