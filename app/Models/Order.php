<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'selling_product_id',
        'seller_id',
        'quantity',
        'total_price',
        'payment_id',
        'payment_screenshot',
        'status',
        'note',
        'reject_note'
    ];

    //connect with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //connect with selling_product
    public function selling_product()
    {
        return $this->belongsTo(SellingProduct::class);
    }

    //connect with seller
    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    //connect with payment
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

}
