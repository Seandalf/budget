<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\TransactionType;
use App\Traits\Audits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// A group transaction is a transaction that can have multiple entries
// For example, I may budget $400 a month for groceries, but enter this as
// 4 $100 transactions as I do my shopping each week
class GroupTransaction extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'description',
        'budget',
        'actual',
        'type',
        'final',
        'interval_id',
        'category_id',
        'payee_id',
        'recurring_transaction_id',
    ];

    protected $casts = [
        'budget' => MoneyCast::class,
        'actual' => MoneyCast::class,
        'type'   => TransactionType::class,
        'final'  => 'boolean',
    ];

    public function interval(): BelongsTo
    {
        return $this->belongsTo(Interval::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(Payee::class);
    }

    public function recurring_transaction(): BelongsTo
    {
        return $this->belongsTo(RecurringTransaction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
