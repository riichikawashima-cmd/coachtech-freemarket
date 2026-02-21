<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 「購入する」ボタンを押下すると購入が完了する()
    {
        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'BUY_ITEM',
        ]);

        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => 'convenience',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience',
        ]);
    }

    /** @test */
    public function プロフィール_購入した商品一覧に追加されている()
    {
        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'SOLD_ITEM',
        ]);

        $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => 'convenience',
        ]);

        $response = $this->get('/?tab=recommend');

        $response->assertStatus(200);
        $response->assertSee('SOLD_ITEM');
        $response->assertSee('Sold');
    }

    /** @test */
    public function 購入済みの商品は購入画面に入れない()
    {
        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);

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
        Profile::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);

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

    /** @test */
    public function 購入した商品はプロフィールの購入一覧に表示される()
    {
        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'BOUGHT_ITEM',
        ]);

        $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => 'convenience',
        ]);

        $response = $this->actingAs($buyer)->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSee('BOUGHT_ITEM');
    }

    /** @test */
    public function 小計画面で変更が反映される()
    {
        $this->withoutExceptionHandling();

        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);

        $item = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer)->postJson(route('purchase.payment_method'), [
            'payment_method' => 'convenience',
            'label' => 'コンビニ支払い',
        ])->assertOk();

        $res = $this->actingAs($buyer)->get("/purchase/{$item->id}");
        $res->assertStatus(200);
        $res->assertSee('コンビニ支払い');
    }
}
