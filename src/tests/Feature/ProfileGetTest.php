<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileGetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインしてプロフィール編集画面を表示できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('プロフィール設定');
    }
}
