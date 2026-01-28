<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常な入力で会員登録できる()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function 名前が未入力だと登録できない()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function メールアドレスが未入力だと登録できない()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー2',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードが未入力だと登録できない()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー3',
            'email' => 'test3@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードが8文字未満だと登録できない()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー4',
            'email' => 'test4@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワード確認が一致しないと登録できない()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー5',
            'email' => 'test5@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors(['password_confirmation']);
    }

    /** @test */
    public function メールアドレスが重複していると登録できない()
    {
        User::create([
            'name' => '既存ユーザー',
            'email' => 'dup@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => '新規ユーザー',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
