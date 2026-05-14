<?php

namespace App\Models;

use App\Traits\ApiResponse;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use LogsActivity;
    use ApiResponse;
    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'subscription_id', 'amount', 'currency',
        'status', 'gateway_transaction_id', 'receipt_url', 'paid_at'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

//     public function getActivityLogOptions(): LogOptions
//    {
//        return LogOptions::defaults()
//            ->logOnly(['subscription_id', 'amount', 'currency', 'status'])
//            ->useLogName('payment');
//    }


}
