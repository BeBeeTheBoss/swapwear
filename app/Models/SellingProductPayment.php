<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingProductPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'selling_product_id',
        'payment_id'
    ];

    //connect with payment
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

}
