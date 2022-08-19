<?php

namespace App\Observers;

use App\Models\Audits\Audit;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditableObserver
{
    protected $previous;
    
    /**
     * Handle the "created" event.
     *
     * @param  \App\Models\ $model
     * @return void
     */
    public function created($model)
    {
        dd($this->previous);
    }

    /**
     * Handle the "updated" event.
     *
     * @param  \App\Models\ $model
     * @return void
     */
    public function updated($model)
    {
        $this->createAuditRecord('updated', $model);
    }

    /**
     * Handle the "deleted" event.
     *
     * @param  \App\Models\ $model
     * @return void
     */
    public function deleted($model)
    {
        //
    }

    /**
     * Handle the "restored" event.
     *
     * @param  \App\Models\ $model
     * @return void
     */
    public function restored($model)
    {
        //
    }

    /**
     * Handle the "force deleted" event.
     *
     * @param  \App\Models\ $model
     * @return void
     */
    public function forceDeleted($model)
    {
        //
    }

    protected function createAuditRecord(string $action, object $model): void
    {
        try {
            $data = [
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'event'          => $action,
                'old_values'     => $action === 'updated' ? json_encode($model->getOriginal()) : null,
                'new_values'     => !in_array($action, ['deleted', 'forceDeleted']) ? json_encode($model) : null,
                'ip_address'     => request()->ip(),
                'user_id'        => Auth::id(),
            ];

            Audit::create($data);
        } catch (Exception $e) {
            Log::error('Could not create audit record', ['error' => $e->getMessage(), 'data' => $data ?? $model]);
        }
    }
}
