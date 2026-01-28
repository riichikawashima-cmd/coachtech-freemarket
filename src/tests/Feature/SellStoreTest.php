<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品確定で商品が登録され画像がitemsに移動される()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $categoryId = DB::table('categories')->insertGetId(['name' => 'カテゴリ1']);
        $conditionId = DB::table('conditions')->insertGetId(['name' => '新品']);

        // ① confirmで一時画像を作る（session sell_confirm.image_path が入る）
        $this->actingAs($user)->post('/sell/confirm', [
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
            'category_ids' => [$categoryId],
            'image' => UploadedFile::fake()->image('item.jpg'),
        ]);

        $tmpPublicPath = session('sell_confirm.image_path'); // storage/items_tmp/xxx.jpg
        $this->assertNotEmpty($tmpPublicPath);

        $tmpRelative = str_replace('storage/', '', $tmpPublicPath); // items_tmp/xxx.jpg
        $this->assertTrue(Storage::disk('public')->exists($tmpRelative));

        // ② store（sessionの一時画像をitemsへmoveしてDB保存）
        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
            'category_ids' => [$categoryId],
            // imageは送らない（confirm経由の想定）
        ]);

        $response->assertRedirect('/');

        // itemsテーブルに登録されている
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
        ]);

        // 登録されたimage_pathを取って、publicに存在することを確認
        $imagePath = DB::table('items')->where('name', 'ITEM_NAME')->value('image_path'); // storage/items/xxx.jpg
        $this->assertNotEmpty($imagePath);

        $relative = str_replace('storage/', '', $imagePath);
        $this->assertTrue(Storage::disk('public')->exists($relative));

        // tmpは消えている（moveなので）
        $this->assertFalse(Storage::disk('public')->exists($tmpRelative));

        // 中間テーブルも入ってる
        $itemId = DB::table('items')->where('name', 'ITEM_NAME')->value('id');
        $this->assertDatabaseHas('category_item', [
            'item_id' => $itemId,
            'category_id' => $categoryId,
        ]);
    }
}
