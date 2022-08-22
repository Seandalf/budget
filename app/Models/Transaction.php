<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'budget',
        'actual',
        'record_number',
        'type',
        'interval_id',
        'category_id',
        'payee_id',
        'recurring_transaction_id',
        'group_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'budget'  => MoneyCast::class,
        'actual'  => MoneyCast::class,
        'type'    => TransactionType::class,
        'paid_at' => 'datetime',
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

    public function group_transaction(): BelongsTo
    {
        return $this->belongsTo(GroupTransaction::class);
    }
}
