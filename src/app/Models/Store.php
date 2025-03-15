<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'store',
        'region_id',
        'genre_id',
        'overview',
        'image',
    ];


    public function reservations(){
        return $this->hasMany('App\Models\Reservation');
    }
    public function favorites(){
        return $this->hasMany('App\Models\Favorite');
    }
    public function storeReviews(){
        return $this->hasMany('App\Models\StoreReview');
    }
    
    
    public function region(){
        return $this->belongsTo('App\Models\Region');
    }
    public function genre(){
        return $this->belongsTo('App\Models\Genre');
    }
}
