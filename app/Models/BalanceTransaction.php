<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    protected $table = 'balance_transaction';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'card_id',
        'user_id',
        'subscription_id',
        'amount',
        'balance',
        'type',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
}
