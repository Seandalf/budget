<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\RecurringTransactionType;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// A recurring transaction is used to create budget entries
// when a new Interval is created. For example, I have monthly
// mortgage payments, when a new Interval is created, I need to add
// that mortgage payment as a budgeted amount
class RecurringTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'recurring_transaction_type',
        'transaction_type',
        'active',
        'budget_id',
        'category_id',
        'payee_id',
        'time_period_id',
        'time_period_amount',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'amount'                     => MoneyCast::class,
        'recurring_transaction_type' => RecurringTransactionType::class,
        'transaction_type'           => TransactionType::class,
        'active'                     => 'boolean',
        'starts_at'                  => 'datetime',
        'ends_at'                    => 'datetime',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(Payee::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
