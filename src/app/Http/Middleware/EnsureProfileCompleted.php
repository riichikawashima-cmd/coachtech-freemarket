<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Profile;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 未ログインは対象外
        if (!$user) {
            return $next($request);
        }

        // メール未認証は対象外（verified側で止める想定）
        if (!$user->hasVerifiedEmail()) {
            return $next($request);
        }

        // プロフィール編集ページは通す（無限ループ防止）
        if ($request->is('mypage/profile')) {
            return $next($request);
        }

        // 必須項目が埋まってるか（住所系）
        $profile = Profile::where('user_id', $user->id)->first();

        $completed = $profile
            && !empty($profile->postal_code)
            && !empty($profile->address);

        if (!$completed) {
            return redirect('/mypage/profile');
        }

        return $next($request);
    }
}
