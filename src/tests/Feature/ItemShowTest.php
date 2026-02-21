<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が表示される()
    {
        DB::table('conditions')->insert([
            'id' => 1,
            'name' => '新品',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId1 = DB::table('categories')->insertGetId([
            'name' => '家電',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId2 = DB::table('categories')->insertGetId([
            'name' => '本',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $item = Item::factory()->create([
            'name' => 'DETAIL_ITEM',
            'brand' => 'BRAND_X',
            'price' => 15000,
            'description' => 'DESC_TEXT',
            'condition' => 1,
            'image_path' => 'images/dummy.png',
        ]);

        DB::table('category_item')->insert([
            ['item_id' => $item->id, 'category_id' => $categoryId1],
            ['item_id' => $item->id, 'category_id' => $categoryId2],
        ]);

        $liker = User::factory()->create();

        Like::create([
            'user_id' => $liker->id,
            'item_id' => $item->id,
        ]);

        $commenter = User::factory()->create([
            'name' => 'COMMENT_USER'
        ]);

        Comment::create([
            'user_id' => $commenter->id,
            'item_id' => $item->id,
            'comment' => 'COMMENT_TEXT',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee('DETAIL_ITEM');
        $response->assertSee('BRAND_X');
        $response->assertSee('¥15,000');
        $response->assertSee('DESC_TEXT');

        $response->assertSee('新品');

        $response->assertSee('家電');
        $response->assertSee('本');

        $response->assertSee('1');

        $response->assertSee('コメント（1）');
        $response->assertSee('COMMENT_USER');
        $response->assertSee('COMMENT_TEXT');

        $response->assertSee('images/dummy.png');
    }

    /** @test */
    public function 複数選択されたカテゴリが表示されているか()
    {
        DB::table('conditions')->insert([
            'id' => 1,
            'name' => '新品',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId1 = DB::table('categories')->insertGetId([
            'name' => '家電',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId2 = DB::table('categories')->insertGetId([
            'name' => '本',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $item = Item::factory()->create([
            'condition' => 1,
        ]);

        DB::table('category_item')->insert([
            ['item_id' => $item->id, 'category_id' => $categoryId1],
            ['item_id' => $item->id, 'category_id' => $categoryId2],
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $html = $response->getContent();

        preg_match('/<ul class="item-categories">(.|\s)*?<\/ul>/', $html, $matches);
        $this->assertNotEmpty($matches, 'カテゴリ一覧が見つかりません');

        $categoryBlock = $matches[0];

        $this->assertStringContainsString('家電', $categoryBlock);
        $this->assertStringContainsString('本', $categoryBlock);
    }
}
