<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'comment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // コメントしたユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // コメントされた商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
