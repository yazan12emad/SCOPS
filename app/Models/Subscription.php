<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'card_id',
        'plan_id',
        'amount',
        'billing_cycle',
        'start_date',
        'renewal_date',
        'status',
        'reminder_days',
        'priority',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
