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

// An interval is a period inside a budget. E.g. in a monthly
// budget, this is a single month period
class Interval extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'opening_balance',
        'closing_balance',
        'income',
        'expenditure',
        'transactions',
        'final',
        'budget_id',
        'user_id',
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

    public function recalculateIncomeExpenditure(): void
    {
        $transactions = Transaction::whereIntervalId($this->id)
                                   ->whereNull('group_transaction_id')
                                   ->get();
        
        $group_transactions = GroupTransaction::whereIntervalId($this->id)->get();

        $income = 0;
        $expenditure = 0;

        foreach ($transactions as $transaction) {
            $amount = $transaction->actual ?? $transaction->budget;

            if ($transaction->type === TransactionType::INCOME) {
                $income += $amount;
            }

            if ($transaction->type === TransactionType::EXPENDITURE) {
                $expenditure += $amount;
            }
        }

        foreach ($group_transactions as $transaction) {
            $amount = $transaction->actual ?? $transaction->budget;

            if ($transaction->type === TransactionType::INCOME) {
                $income += $amount;
            }

            if ($transaction->type === TransactionType::EXPENDITURE) {
                $expenditure += $amount;
            }
        }

        $this->income = $income;
        $this->expenditure = $expenditure;
        $this->save();

        
    }
}
