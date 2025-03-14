<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'stars',
        'comment',
        'image',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 店舗とのリレーション
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}