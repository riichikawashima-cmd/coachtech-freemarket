<?php

namespace App\Actions\Fortify;

use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $request = app(RegisterRequest::class);

        $rules = $request->rules();
        $messages = $request->messages();

        // email の unique はDB制約なのでここで追加
        $rules['email'][] = Rule::unique(User::class);

        Validator::make(
            $input,
            $rules,
            $messages
        )->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // 初回ログイン時ユーザー設定
        Profile::create([
            'user_id' => $user->id,
            'display_name' => $user->name,
        ]);

        return $user;
    }
}
