<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページが表示できる()
    {
        $item = Item::factory()->create(['name' => 'DETAIL_ITEM']);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('DETAIL_ITEM');
    }

    /** @test */
    public function 存在しない商品は404になる()
    {
        $response = $this->get('/item/999999');

        $response->assertStatus(404);
    }
}
