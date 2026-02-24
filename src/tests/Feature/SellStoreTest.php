<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
     */
    public function 出品確定で商品情報が保存され画像がitemsに移動される_カテゴリ_状態_商品名_ブランド_説明_価格()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $categoryId1 = DB::table('categories')->insertGetId([
            'name' => 'カテゴリ1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId2 = DB::table('categories')->insertGetId([
            'name' => 'カテゴリ2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $conditionId = DB::table('conditions')->insertGetId([
            'name' => '新品',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $confirm = $this->actingAs($user)->post('/sell/confirm', [
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
            'category_ids' => [$categoryId1, $categoryId2],
            'image' => UploadedFile::fake()->image('item.jpg'),
        ]);

        $confirm->assertStatus(200);
        $confirm->assertSessionHas('sell_confirm.image_path');

        $tmpPublicPath = session('sell_confirm.image_path');
        $this->assertNotEmpty($tmpPublicPath);

        $tmpRelative = str_replace('storage/', '', $tmpPublicPath);
        $this->assertTrue(Storage::disk('public')->exists($tmpRelative));

        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
            'category_ids' => [$categoryId1, $categoryId2],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
        ]);

        $itemId = DB::table('items')->where('name', 'ITEM_NAME')->value('id');
        $this->assertNotEmpty($itemId);

        $imagePath = DB::table('items')->where('id', $itemId)->value('image_path');
        $this->assertNotEmpty($imagePath);

        $relative = str_replace('storage/', '', $imagePath);
        $this->assertTrue(Storage::disk('public')->exists($relative));

        $this->assertFalse(Storage::disk('public')->exists($tmpRelative));

        $this->assertDatabaseHas('category_item', [
            'item_id' => $itemId,
            'category_id' => $categoryId1,
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $itemId,
            'category_id' => $categoryId2,
        ]);
    }
}
