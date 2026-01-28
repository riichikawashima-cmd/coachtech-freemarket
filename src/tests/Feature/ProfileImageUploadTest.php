<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProfileImageUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール画像をアップロードできる()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => 'AFTER',
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect('/');

        // profilesに画像パスが入る
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
        ]);

        // publicディスクに保存されている
        $path = DB::table('profiles')
            ->where('user_id', $user->id)
            ->value('image_path');

        $this->assertNotNull($path);
        $this->assertTrue(Storage::disk('public')->exists($path));
    }
}
