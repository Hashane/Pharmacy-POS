<?php

namespace Modules\Customer\Entities;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Modules\Order\Entities\CartItem;
use Modules\Order\Entities\Order;
use Modules\Prescription\Entities\Prescription;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'city',
        'country',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    protected static function booted()
    {
        parent::booted();
        static::creating(function ($model) {
            $model->email = strtolower($model->email);
        });
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmailNotification {
            protected function verificationUrl($notifiable)
            {
                return URL::temporarySignedRoute(
                    'customer.verification.verify',
                    now()->addMinutes(config('auth.verification.expire', 60)),
                    [
                        'id' => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ]
                );
            }
        });
    }
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getCartCountAttribute()
    {
        return $this->cartItems()->sum('quantity');
    }
}