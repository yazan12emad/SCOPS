<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'card_holder_name',
        'card_brand',
        'last4',
        'expiry_month',
        'expiry_year',
        'is_primary',
        'tokenized_pan'];
}
