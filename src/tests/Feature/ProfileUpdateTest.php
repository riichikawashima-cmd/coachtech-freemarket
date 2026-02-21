<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 変更項目が初期値として過去設定されていること()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name'  => 'BEFORE',
            'postal_code'   => '111-2222',
            'address'       => '東京都初期区1-1-1',
            'building_name' => '初期ビル',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);

        // 画面のユーザー名は profiles.display_name
        $response->assertSee('value="BEFORE"', false);
        $response->assertSee('value="111-2222"', false);
        $response->assertSee('value="東京都初期区1-1-1"', false);
        $response->assertSee('value="初期ビル"', false);
    }

    /** @test */
    public function プロフィール情報を更新できる()
    {
        $user = User::factory()->create();

        // 先にprofile作っておく（更新対象）
        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'BEFORE',
        ]);

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => 'AFTER',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');

        // profilesテーブル更新確認（display_name が更新される想定）
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'display_name' => 'AFTER',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);
    }
}
