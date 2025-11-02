<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Loyalty\Entities\LoyaltyPoint;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleDetails() {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    public function salePayments() {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = Sale::max('id') + 1;
            $model->reference = make_reference_id('SL', $number);
        });

        static::created(function ($sale) {
            if ($sale->status === 'Completed') {

                // Prevent duplicate points for the same sale
                $alreadyEarned = LoyaltyPoint::where('sale_id', $sale->id)
                    ->where('type', 'earn')
                    ->exists();

                if (! $alreadyEarned) {
                    $points = floor($sale->total_amount / 10); // 1 point per 10 currency units

                    LoyaltyPoint::create([
                        'customer_id' => $sale->customer_id,
                        'sale_id'     => $sale->id,
                        'points'      => $points,
                        'type'        => 'earn',
                        'description' => 'Points earned for Sale #' . $sale->id,
                    ]);
                }
            }
        });
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'Completed');
    }

    public function getShippingAmountAttribute($value) {
        return $value / 100;
    }

    public function getPaidAmountAttribute($value) {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value) {
        return $value / 100;
    }

    public function getDueAmountAttribute($value) {
        return $value / 100;
    }

    public function getTaxAmountAttribute($value) {
        return $value / 100;
    }

    public function getDiscountAmountAttribute($value) {
        return $value / 100;
    }
}
