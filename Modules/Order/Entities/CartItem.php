<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{

    protected $fillable = [
        'customer_user_id',
        'product_id',
        'quantity',
    ];

    public function customerUser()
    {
        return $this->belongsTo(\Modules\People\Entities\CustomerUser::class);
    }

    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class);
    }

    public function getSubTotalAttribute()
    {
        return $this->product->product_price * $this->quantity;
    }
}
