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

    // プロフィール
    public function profile()
    {
        return $this->hasOneThrough(
            Profile::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    // カテゴリ
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

    // 購入情報
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    // 商品状態（conditions）
    public function conditionMaster()
    {
        return $this->belongsTo(Condition::class, 'condition');
    }
}
