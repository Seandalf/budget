<?php

namespace App\Traits\Audits;

use App\Models\Audits\Audit;
use App\Observers\AuditableObserver;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public static function bootAuditable()
    {
        static::observe(AuditableObserver::class);
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
