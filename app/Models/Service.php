<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {
    protected $fillable = ['category_id', 'name', 'logo_url', 'default_amount', 'billing_cycle', 'description'];

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
