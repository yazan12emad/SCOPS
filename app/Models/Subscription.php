<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;

class Subscription extends Model
{
    protected $fillable = [
        'id' ,
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

}
