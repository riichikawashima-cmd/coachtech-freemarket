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

        // いいね
        $res = $this->actingAs($user)->post("/item/{$item->id}/like");
        $res->assertStatus(302);

        // 詳細ページで「アイコン変化」「いいね数増加」を確認
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

        // 先にいいねしておく
        $this->actingAs($user)->post("/item/{$item->id}/like")->assertStatus(302);

        // 解除
        $res = $this->actingAs($user)->delete("/item/{$item->id}/like");
        $res->assertStatus(302);

        // 詳細ページで「アイコン戻る」「いいね数減少」を確認
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

        // いいね前：デフォルト
        $before = $this->actingAs($user)->get("/item/{$item->id}");
        $before->assertStatus(200);
        $before->assertSee('images/heart-default.png');

        // いいね
        $this->actingAs($user)->post("/item/{$item->id}/like")->assertStatus(302);

        // いいね後：liked（色変化）
        $after = $this->actingAs($user)->get("/item/{$item->id}");
        $after->assertStatus(200);
        $after->assertSee('images/heart-liked.png');
    }
}
