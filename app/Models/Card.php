<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $table = 'cards';
    protected $primaryKey = 'card_id';
    protected $fillable = [
          'card_id', 'user_id','card_holder_name', 'card_brand','last4'
        , 'stripe_payment_method_id' , 'expiry_month','expiry_year','is_primary'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
