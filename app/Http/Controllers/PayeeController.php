<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayeeRequest;
use App\Http\Requests\UpdatePayeeRequest;
use App\Models\Payee;

class PayeeController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Payee::class, 'payee');
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
     * @param  \App\Http\Requests\StorePayeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayeeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function show(Payee $payee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function edit(Payee $payee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayeeRequest  $request
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayeeRequest $request, Payee $payee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payee $payee)
    {
        //
    }
}
