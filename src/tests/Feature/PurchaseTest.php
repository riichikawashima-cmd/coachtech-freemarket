<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品を購入できる()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'BUY_ITEM',
        ]);

        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);
    }

    /** @test */
    public function 出品者は自分の商品を購入できない()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $response = $this->actingAs($seller)->post("/purchase/{$item->id}", [
            'payment_method' => 'card',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('purchases', [
            'user_id' => $seller->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 購入済みの商品は購入画面に入れない()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);

        $response = $this->actingAs($buyer)->get("/purchase/{$item->id}");

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['purchase']);
    }

    /** @test */
    public function 支払い方法が未選択だと購入できない()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => '',
        ]);

        $response->assertSessionHasErrors(['payment_method']);

        $this->assertDatabaseMissing('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);
    }
}
