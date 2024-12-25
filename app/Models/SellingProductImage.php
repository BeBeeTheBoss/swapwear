<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'selling_product_id',
        'name',
    ];

    //connect to selling product
    public function selling_product()
    {
        return $this->belongsTo(SellingProduct::class);
    }

}
