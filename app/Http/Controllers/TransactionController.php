<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Interval;

class TransactionController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Transaction::class, 'transaction');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Should be fine for now, but if this ever takes off or is used for a long period of time
        // for the love of god don't do this
        try {
            return successResponse(Transaction::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all transactions');
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
     * @param  \App\Http\Requests\StoreTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionRequest $request)
    {
        try {
            $data = $request->validated();
            $interval = Interval::find($data['interval_id']);
            $budget = $interval->budget;

            $transaction = Transaction::create($data);

            $interval->recalculateIncomeExpenditure();
            $budget->recalculateBalances();

            return successResponse($transaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create transaction');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransactionRequest  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        try {
            $data = $request->validated();
            $updating_amount = $data['actual'] !== $transaction->actual || $data['budget'] !== $transaction->budget;
            $updating_interval = $data['interval_id'] !== $transaction->interval_id;

            $old_interval = $updating_interval ? $transaction->interval : null;
            $interval = Interval::find($data['interval_id']);
            $budget = $interval->budget;

            $transaction = Transaction::create($data);

            if ($updating_amount || $updating_interval) {
                if ($old_interval) {
                    $old_interval->recalculateIncomeExpenditure();
                }

                $interval->recalculateIncomeExpenditure();
                $budget->recalculateBalances();
            }

            return successResponse($transaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update transaction');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $interval = $transaction->interval;
            $budget = $interval->budget;

            $transaction->delete();

            $interval->recalculateIncomeExpenditure();
            $budget->recalculateBalances();

            return successResponse($transaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete transaction');
        }
    }
}
