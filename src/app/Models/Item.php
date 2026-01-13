<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'description',
        'price',
        'condition',
        'image_path',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 出品者（users）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // プロフィール（間接的に使うこと多い）
    public function profile()
    {
        return $this->hasOneThrough(
            Profile::class,
            User::class,
            'id',        // users.id
            'user_id',   // profiles.user_id
            'user_id',   // items.user_id
            'id'         // users.id
        );
    }

    // カテゴリ（多対多）
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // いいね
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入情報（1商品1購入）
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
