<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validated();

        // users テーブル（名前）
        $user->update([
            'name' => $data['name'],
        ]);

        // profiles テーブル（住所系）
        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $data['postal_code'] ?? null,
                'address' => $data['address'] ?? null,
                'building_name' => $data['building_name'] ?? null,
            ]
        );

        // プロフィール画像保存
        if ($request->hasFile('image')) {
            // 既存画像があれば削除（安全）
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }

            $path = $request->file('image')->store('profiles', 'public');
            $profile->update([
                'image_path' => $path,
            ]);
        }

        return redirect('/mypage');
    }
}
