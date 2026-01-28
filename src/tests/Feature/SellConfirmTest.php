<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SellConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品確認で画像が一時保存されセッションに入る()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // categories / conditions はDB参照されるので用意
        $categoryId = DB::table('categories')->insertGetId(['name' => 'カテゴリ1']);
        $conditionId = DB::table('conditions')->insertGetId(['name' => '新品']);

        $response = $this->actingAs($user)->post('/sell/confirm', [
            'name' => 'ITEM_NAME',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => $conditionId,
            'category_ids' => [$categoryId],
            'image' => UploadedFile::fake()->image('item.jpg'),
        ]);

        $response->assertStatus(200);

        $response->assertSessionHas('sell_confirm.image_path');

        $path = session('sell_confirm.image_path'); // storage/items_tmp/xxx.jpg
        $this->assertNotEmpty($path);

        $this->assertTrue(Storage::disk('public')->exists(str_replace('storage/', '', $path)));
    }
}
