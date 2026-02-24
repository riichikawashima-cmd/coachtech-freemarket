<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileGetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'SELL_ITEM',
        ]);

        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $seller->id]);

        $boughtItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'BOUGHT_ITEM',
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $boughtItem->id,
            'payment_method' => 'convenience',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);

        $response->assertSee('テストユーザー');

        $response->assertSee('SELL_ITEM');

        $buyPage = $this->actingAs($user)->get('/mypage?page=buy');
        $buyPage->assertStatus(200);
        $buyPage->assertSee('BOUGHT_ITEM');
    }
}
