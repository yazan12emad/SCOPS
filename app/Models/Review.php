<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'service_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
