<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupTransactionRequest;
use App\Http\Requests\UpdateGroupTransactionRequest;
use App\Models\GroupTransaction;

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
     * @param  \App\Http\Requests\StoreGroupTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupTransactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GroupTransaction  $groupTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(GroupTransaction $groupTransaction)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GroupTransaction  $groupTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(GroupTransaction $groupTransaction)
    {
        //
    }
}
