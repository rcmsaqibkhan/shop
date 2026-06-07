<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'image',
        'status',
        'softDeletes',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}
