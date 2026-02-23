<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->count() === 0) {

            $user1Id = DB::table('users')->insertGetId([
                'name' => 'testuser1',
                'email' => 'test1@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user2Id = DB::table('users')->insertGetId([
                'name' => 'testuser2',
                'email' => 'test2@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user3Id = DB::table('users')->insertGetId([
                'name' => 'testuser3',
                'email' => 'test3@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('profiles')->insert([
                [
                    'user_id' => $user1Id,
                    'image_path' => null,
                    'display_name' => 'testuser1',
                    'postal_code' => '530-0001',
                    'address' => '大阪府大阪市北区',
                    'building_name' => 'テストビル101',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $user2Id,
                    'image_path' => null,
                    'display_name' => 'testuser2',
                    'postal_code' => '150-0001',
                    'address' => '東京都渋谷区',
                    'building_name' => 'テストマンション202',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $user3Id,
                    'image_path' => null,
                    'display_name' => 'testuser3',
                    'postal_code' => '460-0001',
                    'address' => '愛知県名古屋市中区',
                    'building_name' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (DB::table('conditions')->count() === 0) {
            DB::table('conditions')->insert([
                ['name' => '良好', 'created_at' => now(), 'updated_at' => now()],
                ['name' => '目立った傷や汚れなし', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'やや傷や汚れあり', 'created_at' => now(), 'updated_at' => now()],
                ['name' => '状態が悪い', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

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

        if (DB::table('items')->count() === 0) {

            $userIds = DB::table('users')->orderBy('id')->pluck('id')->toArray();

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

            $cat = DB::table('categories')->pluck('id', 'name')->toArray();

            $insertedItemIds = [];
            foreach ($items as $index => $item) {
                $insertedItemIds[] = DB::table('items')->insertGetId([
                    'user_id' => $userIds[$index % count($userIds)],
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

            $map = [
                0 => ['メンズ', 'アクセサリー'],
                1 => ['家電'],
                2 => ['キッチン'],
                3 => ['メンズ', 'ファッション'],
                4 => ['家電'],
                5 => ['家電'],
                6 => ['レディース', 'ファッション'],
                7 => ['キッチン'],
                8 => ['キッチン'],
                9 => ['レディース', 'コスメ'],
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
