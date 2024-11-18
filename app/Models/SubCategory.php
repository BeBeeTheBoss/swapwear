<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['main_category_id', 'name', 'purchase_count'];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }
}
