<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// An interval is a period inside a budget. E.g. in a monthly
// budget, this is a single month period
class Interval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opening_balance',
        'closing_balance',
        'income',
        'expenditure',
        'transactions',
        'final',
        'budget_id',
        'time_period_id',
        'time_period_amount',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'opening_balance' => MoneyCast::class,
        'closing_balance' => MoneyCast::class,
        'income'          => MoneyCast::class,
        'expenditure'     => MoneyCast::class,
        'final'           => 'boolean',
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function group_transactions(): HasMany
    {
        return $this->hasMany(GroupTransaction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactionsByCategory(): array
    {
        // TODO
        return [];
    }
}
