<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        Item::factory()->create(['name' => 'AAA']);
        Item::factory()->create(['name' => 'BBB']);
        Item::factory()->create(['name' => 'CCC']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('AAA');
        $response->assertSee('BBB');
        $response->assertSee('CCC');
    }

    /** @test */
    public function 購入済み商品は「Sold」と表示される()
    {
        $item = Item::factory()->create(['name' => 'SOLD_ITEM']);

        $buyer = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $buyer->id,
            'display_name' => 'テスト購入者',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SOLD_ITEM');
        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $me = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $me->id,
            'display_name' => 'テスト出品者',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        Item::factory()->create([
            'user_id' => $me->id,
            'name' => 'MY_ITEM',
        ]);

        $other = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $other->id,
            'display_name' => 'テスト他人',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        Item::factory()->create([
            'user_id' => $other->id,
            'name' => 'OTHER_ITEM',
        ]);

        $response = $this->actingAs($me)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('MY_ITEM');
        $response->assertSee('OTHER_ITEM');
    }
}
