<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 購入者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 購入された商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
