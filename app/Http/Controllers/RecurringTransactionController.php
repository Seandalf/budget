<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringTransactionRequest;
use App\Http\Requests\UpdateRecurringTransactionRequest;
use App\Models\GroupTransaction;
use App\Models\Interval;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\Auth;

class RecurringTransactionController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(RecurringTransaction::class, 'recurring_transaction');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return successResponse(RecurringTransaction::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all recurring transactions');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRecurringTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecurringTransactionRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['amount'] = $data['amount'] * 100;
            $recurringTransaction = RecurringTransaction::create($data);

            $budget = $recurringTransaction->budget;
            $intervals = $budget->intervals;

            $every = $recurringTransaction->time_period_amount ?? 1;
            $time_period = parseTimePeriod($recurringTransaction->time_period->name);
            $ends_at = $recurringTransaction->ends_at ?? Interval::whereBudgetId($budget->id)
                                                                 ->orderBy('starts_at', 'desc')
                                                                 ->pluck('ends_at')
                                                                 ->first();

            $occurrences = CarbonPeriod::create($recurringTransaction->starts_at, "{$every} {$time_period}", $ends_at)->toArray();

            foreach ($occurrences as $occur) {
                $matching_intervals = array_filter($intervals->toArray(), function ($interval) use ($occur) {
                    return Carbon::create($interval['starts_at'])->lte($occur) && Carbon::create($interval['ends_at'])->gte($occur);
                });

                $interval = reset($matching_intervals);

                $transaction_data = [
                    'name' => $recurringTransaction->name,
                    'description' => "{$recurringTransaction->description} - {$occur->toDateString()}",
                    'budget' => $recurringTransaction->amount,
                    'type' => $recurringTransaction->transaction_type,
                    'user_id' => Auth::id(),
                    'interval_id' => $interval['id'],
                    'category_id' => $recurringTransaction->category_id,
                    'payee_id' => $recurringTransaction->payee_id,
                    'recurring_transaction_id' => $recurringTransaction->id,
                    'due_at' => $occur->toDateString(),
                ];

                Transaction::create($transaction_data);
            }

            foreach ($intervals as $interval) {
                $interval->recalculateIncomeExpenditure();
            }

            $budget->recalculateBalances();

            return successResponse($recurringTransaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create recurring transaction');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(RecurringTransaction $recurringTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(RecurringTransaction $recurringTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRecurringTransactionRequest  $request
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecurringTransactionRequest $request, RecurringTransaction $recurringTransaction)
    {
        try {
            $data = $request->validated();
            $updating_amount = $data['amount'] != $recurringTransaction->amount;
            $updating_frequency = $data['time_period_id'] !== $recurringTransaction->time_period_id || $data['time_period_amount'] !== $recurringTransaction->time_period_amount;
            $transactions = $recurringTransaction->transactions;
            $budget = $recurringTransaction->budget;
            $intervals = $budget->intervals;

            if ($updating_amount) {
                $data['amount'] = $data['amount'] * 100;
            }
            $recurringTransaction->update($data);

            // if updating the amounts, need to update all non-paid transactions
            if ($updating_amount) {
                foreach ($transactions as $transaction) {
                    if (! $transaction->actual) {
                        // Only update the budgeted transactions. Paid ones have been paid and shouldn't be changed by this.
                        $transaction->budget = $recurringTransaction->amount;
                        $transaction->save();
                    }
                }
            }

            // If updating the frequency, need to destroy all non-paid and recreate
            if ($updating_frequency) {
                foreach ($transactions as $transaction) {
                    if (! $transaction->actual) {
                        $transaction->delete();
                    }
                }

                $recurringTransaction->load('transactions');
                $transactions = $recurringTransaction->transactions;
                $start_cut_off_date = end($transactions)->due_at;

                $every = $this->time_period_amount ?? 1;
                $time_period = parseTimePeriod($this->time_period->name);
                $ends_at = $recurringTransaction->ends_at ?? Interval::whereBudgetId($budget->id)
                                                                     ->orderBy('starts_at', 'desc')
                                                                     ->pluck('ends_at')
                                                                     ->first();

                $occurrences = CarbonPeriod::create($recurringTransaction->starts_at, "{$every} {$time_period}", $ends_at)->toArray();

                foreach ($occurrences as $occur) {
                    if ($occur->lte($start_cut_off_date)) {
                        continue;
                    }

                    $matching_intervals = array_filter($intervals->toArray(), function ($interval) use ($occur) {
                        return Carbon::create($interval['starts_at'])->lte($occur) && Carbon::create($interval['ends_at'])->gte($occur);
                    });

                    $interval = reset($matching_intervals);

                    $transaction_data = [
                        'name' => $recurringTransaction->name,
                        'description' => "{$recurringTransaction->description} - {$occur->toDateString()}",
                        'budget' => $recurringTransaction->amount,
                        'type' => $recurringTransaction->transaction_type,
                        'user_id' => Auth::id(),
                        'interval_id' => $interval['id'],
                        'category_id' => $recurringTransaction->category_id,
                        'payee_id' => $recurringTransaction->payee_id,
                        'recurring_transaction_id' => $recurringTransaction->id,
                        'due_at' => $occur->toDateString(),
                    ];

                    Transaction::create($transaction_data);
                }
            }

            if ($updating_amount || $updating_frequency) {
                foreach ($budget->intervals as $interval) {
                    $interval->recalculateIncomeExpenditure();
                }

                $budget->recalculateBalances();
            }

            return successResponse(RecurringTransaction::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update recurring transaction');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecurringTransaction $recurringTransaction)
    {
        try {
            $transactions = $recurringTransaction->transactions;
            $recurringTransaction->delete();

            // Destroy all non-paid
            foreach ($transactions as $transaction) {
                if (! $transaction->actual) {
                    $transaction->delete();
                }
            }

            return successResponse($recurringTransaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete recurring transaction');
        }
    }
}
