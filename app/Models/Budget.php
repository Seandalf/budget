<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 
        'description',
        'opening_balance',
        'closing_balance',
        'future_intervals',
        'active',
        'user_id',
        'currency_id',
        'time_period_id',
        'start_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'active' => 'boolean',
        'opening_balance' => MoneyCast::class,
        'closing_balance' => MoneyCast::class,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function intervals(): HasMany
    {
        return $this->hasMany(Interval::class);
    }

    public function recurring_transactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function group_transactions(): HasManyThrough
    {
        return $this->hasManyThrough(GroupTransaction::class, Interval::class);
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Interval::class);
    }
}
