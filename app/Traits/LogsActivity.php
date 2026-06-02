<?php

namespace App\Traits;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity {
    // the bootLogsActivity method is automatically called by Laravel when the model boots up
    public static function bootLogsActivity(): void
    {
        $ActivityLogService = app(ActivityLogService::class);

        static::created(function($model) use ($ActivityLogService){
            $ActivityLogService->log('created', $model, [
                'attributes' => $model->getAttributes(),
            ]);
        });

        static::updated(function (Model $model) use ($ActivityLogService): void {
            $ActivityLogService->log('updated', $model, [
                'changed' => $model->getChanges(),
                'original' => $model->getOriginal(),
            ]);
        });

        static::deleted(function (Model $model) use ($ActivityLogService): void {
            $ActivityLogService->log('deleted', $model, [
                'attributes' => $model->getAttributes(),
            ]);


        });
    }
    public function logActivity(String $action_type, array $meta = []): void
    {
        $ActivityLogService = app(ActivityLogService::class);

        $ActivityLogService->log($action_type, $this, $meta);
    }

}
