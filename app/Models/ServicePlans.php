<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePlans extends Model
{
    protected $fillable =[
        'service_id',
        'plan_name',
        'price',
        'billing_cycle',
        'features'];
}
