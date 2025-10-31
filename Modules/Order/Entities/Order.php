<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Entities\CustomerUser;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_user_id',
        'reference',
        'status',
        'total_amount',
        'notes',
        'admin_notes',
        'ready_at',
        'completed_at',
    ];

    protected $casts = [
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function customerUser()
    {
        return $this->belongsTo(CustomerUser::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateReference()
    {
        $latest = self::latest('id')->first();
        $number = $latest ? $latest->id + 1 : 1;
        return 'ORD-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePreparing($query)
    {
        return $query->where('status', 'preparing');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'preparing' => 'info',
            'ready' => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger',
        ];
        return $badges[$this->status] ?? 'default';
    }
}