<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sub_category_id',
        'name',
        'description',
        'condition',
        'quantity',
        'price',
        'status',
        'is_active'
    ];

    //connect with payments
    public function payments(){
        return $this->hasMany(SellingProductPayment::class);
    }

    //connect with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //connect with subcategory
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }

    //connect with selling product images
    public function images()
    {
        return $this->hasMany(SellingProductImage::class);
    }

}
