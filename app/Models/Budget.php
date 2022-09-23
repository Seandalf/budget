<?php

namespace App\Models;

use App\Traits\Audits\Auditable;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes, Auditable;

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
        'time_period_amount',
        'starts_at',
    ];

    protected $casts = [
        'starts_at'       => 'datetime',
        'active'          => 'boolean',
    ];

    protected $appends = ['current_period'];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function intervals(): HasMany
    {
        return $this->hasMany(Interval::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function time_period(): BelongsTo
    {
        return $this->belongsTo(TimePeriod::class);
    }

    public function group_transactions(): HasManyThrough
    {
        return $this->hasManyThrough(GroupTransaction::class, Interval::class);
    }

    public function recurring_transactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Interval::class);
    }

    public function intervalPeriods(): array
    {
        $start_date = Carbon::create($this->starts_at);
        $every = $this->time_period_amount ?? 1;
        $time_period = parseTimePeriod($this->time_period->name);
        $end_date = Carbon::create($this->starts_at)->add($time_period, $every * $this->future_intervals)->subDay();

        return CarbonPeriod::create($start_date, "{$every} {$time_period}", $end_date)->toArray();
    }

    public function recalculateBalances()
    {
        $intervals = Interval::whereBudgetId($this->id)->orderBy('starts_at', 'asc')->get();

        $last_period = null;
        foreach ($intervals as $interval) {
            if (! $last_period) {
                $interval->opening_balance = $this->opening_balance;
            } else {
                $interval->opening_balance = $last_period->closing_balance;
            }
            $last_period = $interval;

            if ($interval->final) {
                // If this budget interval has been finalised, don't change anything
                continue;
            }

            $interval->closing_balance = $interval->opening_balance + $interval->income - $interval->expenditure;
            $interval->save();
        }
    }

    protected function currentPeriod(): Attribute
    {
        return Attribute::make(
            get: function () {
                foreach ($this->intervals as $interval) {
                    if (Carbon::create($interval->starts_at)->lte(now()->startOfDay()) && Carbon::create($interval->ends_at)->gte(now()->startOfDay())) {
                        return $interval;
                    }
                }
            },
        );
    }
}
