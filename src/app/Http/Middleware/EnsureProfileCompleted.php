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

        if (!$user) {
            return $next($request);
        }

        if (!$user->hasVerifiedEmail()) {
            return $next($request);
        }

        if ($request->is('mypage/profile')) {
            return $next($request);
        }

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
