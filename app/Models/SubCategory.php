<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['main_category_id', 'name', 'icon','purchase_count'];

    public function main_category()
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function selling_products(){
        return $this->hasMany(SellingProduct::class);
    }

}
