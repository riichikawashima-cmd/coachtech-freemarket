<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '111-1111',
            'address' => '東京都テスト区0-0-0',
            'building_name' => 'テストビル0',
        ]);

        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $response->assertRedirect("/purchase/{$item->id}");

        $response->assertSessionHas('purchase_address', [
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);
    }

    /** @test */
    public function 購入した商品に配送先住所が紐づいている()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '111-1111',
            'address' => '東京都テスト区0-0-0',
            'building_name' => 'テストビル0',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ])->assertRedirect("/purchase/{$item->id}");

        $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'convenience',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);
    }
}
