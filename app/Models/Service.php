<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Service extends Model {
    use LogsActivity;

    protected $fillable = ['category_id', 'name', 'logo_url', 'default_amount', 'billing_cycle', 'description'];

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
