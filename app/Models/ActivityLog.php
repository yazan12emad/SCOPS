<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'log_id';

    /** @use HasFactory<\Database\Factories\ActivityLogFactory> */
    use HasFactory;
    // We didn't need the updated_at column so we set timestamps to false
    // we have just created_at column to log the time of the activity
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'actor_type',
        'action_type',
        'entity_type',
        'entity_id',
        'meta_json',
        'created_at',
    ];

}
