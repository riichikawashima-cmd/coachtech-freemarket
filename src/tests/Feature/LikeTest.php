<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンを押下することによって、いいねした商品として登録することができる。()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post("/item/{$item->id}/like");
        $res->assertStatus(302);

        $show = $this->actingAs($user)->get("/item/{$item->id}");
        $show->assertStatus(200);

        $show->assertSee('images/heart-liked.png');
        $show->assertSee('<span class="icon-count">1</span>', false);
    }

    /** @test */
    public function 再度いいねアイコンを押下することによって、いいねを解除することができる。()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user)->post("/item/{$item->id}/like")->assertStatus(302);

        $res = $this->actingAs($user)->delete("/item/{$item->id}/like");
        $res->assertStatus(302);

        $show = $this->actingAs($user)->get("/item/{$item->id}");
        $show->assertStatus(200);

        $show->assertSee('images/heart-default.png');
        $show->assertSee('<span class="icon-count">0</span>', false);
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        $before = $this->actingAs($user)->get("/item/{$item->id}");
        $before->assertStatus(200);
        $before->assertSee('images/heart-default.png');

        $this->actingAs($user)->post("/item/{$item->id}/like")->assertStatus(302);

        $after = $this->actingAs($user)->get("/item/{$item->id}");
        $after->assertStatus(200);
        $after->assertSee('images/heart-liked.png');
    }
}
