<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        $data = $request->validated();

        DB::table('comments')->insert([
            'user_id'    => Auth::id(),
            'item_id'    => $item_id,
            'comment'    => $data['comment'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back();
    }
}
