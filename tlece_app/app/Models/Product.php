<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'quantity',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtDiffAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

}
