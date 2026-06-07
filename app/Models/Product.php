<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'code',
        'buy_price',
        'sell_price',
        'buy_date',
        'supplier_id',
        'image',
        'quantity',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
