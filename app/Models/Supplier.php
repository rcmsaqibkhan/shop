<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    protected $table = "suppliers";
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'image',
        'shop_name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
