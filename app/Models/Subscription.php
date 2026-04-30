<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable=[
        'name',
        'plan_name',
        'price',
        'billing_cycle',
        'status',
        'priority',
        'renewal_date',
        'reminder_days',
    ];
}
