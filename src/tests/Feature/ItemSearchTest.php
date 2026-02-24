<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Like;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 「商品名」で部分一致検索ができる()
    {
        Item::factory()->create(['name' => 'Apple iPhone']);
        Item::factory()->create(['name' => 'Banana Case']);

        $response = $this->get('/?tab=recommend&keyword=Apple');

        $response->assertStatus(200);
        $response->assertSee('Apple iPhone');
        $response->assertDontSee('Banana Case');
    }

    /** @test */
    public function 検索状態がマイリストでも保持される()
    {
        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $item = Item::factory()->create([
            'name' => 'Apple Watch',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)->get('/?tab=recommend&keyword=Apple');

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=Apple');

        $response->assertStatus(200);
        $response->assertSee('Apple Watch');
    }
}
