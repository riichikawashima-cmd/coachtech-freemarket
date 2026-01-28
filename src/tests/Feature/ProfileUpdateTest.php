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
    public function プロフィール情報を更新できる()
    {
        $user = User::factory()->create(['name' => 'BEFORE']);

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => 'AFTER',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
            // imageは今回は省略（別テストでやる）
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'AFTER',
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ]);
    }
}
