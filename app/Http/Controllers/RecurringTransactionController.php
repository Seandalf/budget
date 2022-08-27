<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringTransactionRequest;
use App\Http\Requests\UpdateRecurringTransactionRequest;
use App\Models\RecurringTransaction;
use Exception;

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
            // Need to create the transactions and add them to the intervals
            return successResponse(RecurringTransaction::all());
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
            // if updating the amounts, need to update the interval totals
            // If updating the frequency, need to destroy all in future and recreate, update interval totals
            // If being made inactive, need to destroy all in future
            // If moving end date, need to correct
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
            $recurringTransaction->delete();
            // Destroy all in future
            return successResponse($recurringTransaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete recurring transaction');
        }
    }
}
