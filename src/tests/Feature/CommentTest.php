<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインしてコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => 'テストコメント',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    /** @test */
    public function 未ログインだとコメントできず会員登録画面へリダイレクトされる()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'テストコメント',
        ]);

        $response->assertRedirect('/register');
    }
}
