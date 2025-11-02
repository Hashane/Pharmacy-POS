<?php

namespace Modules\Loyalty\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Loyalty\Database\factories\LoyaltyFactory;
use Modules\Order\Entities\Order;
use Modules\People\Entities\Customer;

class LoyaltyPoint extends Model
{
    protected $fillable = ['customer_id', 'sale_id', 'points', 'type', 'description'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
