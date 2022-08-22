<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringTransactionRequest;
use App\Http\Requests\UpdateRecurringTransactionRequest;
use App\Models\RecurringTransaction;

class RecurringTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecurringTransaction $recurringTransaction)
    {
        //
    }
}
