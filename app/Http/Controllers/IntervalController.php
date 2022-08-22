<?php

namespace App\Http\Controllers;

use App\Models\Interval;
use App\Http\Requests\StoreIntervalRequest;
use App\Http\Requests\UpdateIntervalRequest;

class IntervalController extends Controller
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
     * @param  \App\Http\Requests\StoreIntervalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIntervalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function show(Interval $interval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function edit(Interval $interval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateIntervalRequest  $request
     * @param  \App\Models\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateIntervalRequest $request, Interval $interval)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Interval  $interval
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interval $interval)
    {
        //
    }
}
