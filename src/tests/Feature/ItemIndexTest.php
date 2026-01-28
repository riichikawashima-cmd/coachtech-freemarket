<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品一覧で全商品が表示される()
    {
        $a = Item::factory()->create(['name' => 'AAA']);
        $b = Item::factory()->create(['name' => 'BBB']);
        $c = Item::factory()->create(['name' => 'CCC']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('AAA');
        $response->assertSee('BBB');
        $response->assertSee('CCC');
    }
}
