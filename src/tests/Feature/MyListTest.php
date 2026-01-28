<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 自分がいいねした商品だけがマイリストに表示される()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $likedItem = Item::factory()->create(['name' => 'LIKED']);
        $notLikedItem = Item::factory()->create(['name' => 'NOT_LIKED']);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        Like::create([
            'user_id' => $otherUser->id,
            'item_id' => $notLikedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('LIKED');
        $response->assertDontSee('NOT_LIKED');
    }
}
