<?php

namespace App\Models;

use App\Traits\Audits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'shortcode',
    ];

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
