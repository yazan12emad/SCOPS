<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use LogsActivity;

    protected $fillable = ['name', 'description'];

    public function services() {
        return $this->hasMany(Service::class);
    }
}
