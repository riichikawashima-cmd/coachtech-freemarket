<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function キーワードで商品名検索できる()
    {
        Item::factory()->create(['name' => 'Apple iPhone']);
        Item::factory()->create(['name' => 'Banana Case']);

        $response = $this->get('/?tab=recommend&keyword=Apple');

        $response->assertStatus(200);
        $response->assertSee('Apple iPhone');
        $response->assertDontSee('Banana Case');
    }
}
