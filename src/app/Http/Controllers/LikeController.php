<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function store($item_id)
    {
        DB::table('likes')->updateOrInsert(
            ['user_id' => Auth::id(), 'item_id' => $item_id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return back();
    }

    public function destroy($item_id)
    {
        DB::table('likes')
            ->where('user_id', Auth::id())
            ->where('item_id', $item_id)
            ->delete();

        return back();
    }
}
