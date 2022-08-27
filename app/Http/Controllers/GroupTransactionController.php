<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupTransactionRequest;
use App\Http\Requests\UpdateGroupTransactionRequest;
use App\Models\GroupTransaction;
use App\Models\Interval;
use Exception;

class GroupTransactionController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(GroupTransaction::class, 'group_transaction');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return successResponse(GroupTransaction::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all group transactions');
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
     * @param  \App\Http\Requests\StoreGroupTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupTransactionRequest $request)
    {
        try {
            $data = $request->validated();

            $interval = Interval::find($data['interval_id']);
            $budget = $interval->budget;

            $groupTransaction = GroupTransaction::create($data);

            $interval->recalculateIncomeExpenditure();
            $budget->recalculateBalances();

            return successResponse($groupTransaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create group transactions');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GroupTransaction  $groupTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(GroupTransaction $groupTransaction)
    {
        try {
            return successResponse(GroupTransaction::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all group transactions');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GroupTransaction  $groupTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(GroupTransaction $groupTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGroupTransactionRequest  $request
     * @param  \App\Models\GroupTransaction  $groupTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGroupTransactionRequest $request, GroupTransaction $groupTransaction)
    {
        try {
            $interval = $groupTransaction->interval;
            $budget = $interval->budget;
            $data = $request->validated();
            $updating_amount = $data['amount'] != $groupTransaction->amount;

            $groupTransaction->update($data);

            if ($updating_amount) {
                $interval->recalculateIncomeExpenditure();
                $budget->recalculateBalances();
            }

            return successResponse(GroupTransaction::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all group transactions');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GroupTransaction  $groupTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(GroupTransaction $groupTransaction)
    {
        try {
            $interval = $groupTransaction->interval;
            $budget = $interval->budget;

            $groupTransaction->delete();

            $interval->recalculateIncomeExpenditure();
            $budget->recalculateBalances();

            return successResponse($groupTransaction);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete group transactions');
        }
    }
}
