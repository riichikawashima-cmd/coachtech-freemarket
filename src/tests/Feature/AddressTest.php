<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 配送先を変更するとセッションに保存され購入画面へリダイレクトされる()
    {
        $user = User::factory()->create();
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
}
