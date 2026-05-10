<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(String $action_type ,Model $entity, array $meta = [] ): void
    {
        // $action_type: create, update, delete
        // $entity: the model instance that the action is performed on
        // $meta: additional data related to the action, stored as JSON

        $user = auth()->user();

        ActivityLog::create([
            'user_id' => $user?->user_id,
            'actor_type' => $user ? 'user' : 'system',
            'action_type' => $action_type,
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->getKey(),
            'meta_json' => json_encode($meta),
        ]);
    }

}
