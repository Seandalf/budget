<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Traits\Audits\Auditable;
use Carbon\Carbon;
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
        'final'           => 'boolean',
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactionsByCategory(): array
    {
        $all_transactions = $this->transactions()->whereNull('group_transaction_id')->with('category')->get();

        $all_categories = $this->budget->categories;

        $categories = [
            'income' => [
                'total' => 0,
                'items' => [],
            ],
            'expenditure' => [
                'total' => 0,
                'items' => [],
            ],
        ];

        foreach ($all_categories as $category) {
            $transaction_type = $category['type'] === TransactionType::INCOME ? 'income' : 'expenditure';
            $categories[$transaction_type]['items'][$category['name']] = [
                'budget' => 0,
                'actual' => 0,
                'view'   => false,
                'items'  => [],
            ];
        }

        foreach ($all_transactions as $transaction) {
            $transaction_type = $transaction['type'] === TransactionType::INCOME ? 'income' : 'expenditure';
            $categories[$transaction_type]['items'][$transaction['category']['name']]['items'][] = $transaction->toArray();
        }

        foreach ($categories['income']['items'] as &$category) {
            $budget = 0;
            $actual = 0;

            foreach ($category['items'] as $item) {
                $budget += $item['budget'];
                $actual += $item['actual'];

                $categories['income']['total'] += $item['actual'] ?? $item['budget'];
            }

            $category['budget'] = $budget;
            $category['actual'] = $actual;
        }

        foreach ($categories['expenditure']['items'] as &$category) {
            $budget = 0;
            $actual = 0;

            foreach ($category['items'] as $item) {
                $budget += $item['budget'];
                $actual += $item['actual'];

                $categories['expenditure']['total'] += $item['actual'] ?? $item['budget'];
            }

            $category['budget'] = $budget;
            $category['actual'] = $actual;
        }

        return $categories;
    }

    public function recalculateIncomeExpenditure(): void
    {
        if ($this->final) {
            // If this budget interval has been finalised, don't change anything
            return;
        }

        $transactions = $this->transactions;

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

        $this->income = $income;
        $this->expenditure = $expenditure;
        $this->save();
    }

    public function statistics($category_breakdown)
    {
        return [
            'is_current' => Carbon::create($this->starts_at)->lte(now()) && Carbon::create($this->ends_at)->gte(now()),
            'current_bank_balance' => $this->current_bank_balance($category_breakdown),
            'remaining_income' => $this->remaining('income', $category_breakdown),
            'remaining_expenditure' => $this->remaining('expenditure', $category_breakdown),
        ];
    }

    public function current_bank_balance($category_breakdown): int
    {
        $actual_income = 0;
        foreach ($category_breakdown['income']['items'] as $category) {
            $actual_income += $category['actual'];
        }

        $actual_spend = 0;
        foreach ($category_breakdown['expenditure']['items'] as $category) {
            $actual_spend += $category['actual'];
        }

        return $this->opening_balance + $actual_income - $actual_spend;
    }

    public function remaining($type, $category_breakdown): int
    {
        $remaining = 0;
        foreach ($category_breakdown[$type]['items'] as $category) {
            foreach ($category['items'] as $item) {
                if (! is_numeric($item['actual'])) {
                    $remaining += $item['budget'];
                }
            }
        }

        return $remaining;
    }
}
