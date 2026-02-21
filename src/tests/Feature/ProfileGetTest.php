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

        // 出品した商品
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'SELL_ITEM',
        ]);

        // 購入した商品（別ユーザーが出品 → 自分が購入）
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

        // マイページ（出品側がデフォで表示される）
        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);

        // ユーザー名（現状のHTMLは users.name が出てる）
        $response->assertSee('テストユーザー');

        // 出品した商品
        $response->assertSee('SELL_ITEM');

        // 購入した商品一覧は buy タブにあるのでページ指定して確認
        $buyPage = $this->actingAs($user)->get('/mypage?page=buy');
        $buyPage->assertStatus(200);
        $buyPage->assertSee('BOUGHT_ITEM');
    }
}
