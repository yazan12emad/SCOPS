<?php

namespace App\Models;

use App\Traits\ApiResponse;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use LogsActivity;
    use ApiResponse;
    protected $primaryKey = 'payment_id';
    protected $table = 'payments';
    protected $fillable = [
        'user_id',
        'service_id',
        'plan_id',
        'subscription_id',
        'service_email' ,
        'amount',
        'status',
        'gateway_reference',
        'receipt_url',
        'currency' ,
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ServicePlans::class, 'plan_id');
    }




}
