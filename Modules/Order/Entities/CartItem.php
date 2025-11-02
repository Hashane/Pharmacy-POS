<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Product;

class CartItem extends Model
{

    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubTotalAttribute()
    {
        return $this->product->product_price * $this->quantity;
    }
}
