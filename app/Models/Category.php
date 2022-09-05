<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Traits\Audits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'type',
        'budget_id',
        'user_id',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    protected function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    protected function recurring_transactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    protected function group_transactions(): HasMany
    {
        return $this->hasMany(GroupTransaction::class);
    }

    protected function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
