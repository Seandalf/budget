<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Currency;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;

class CurrencyController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Currency::class, 'currency');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return successResponse(Currency::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all currency');
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
     * @param  \App\Http\Requests\StoreCurrencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCurrencyRequest $request)
    {
        try {
            $currency = Currency::create($request->validated());
            return successResponse($currency);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create currency');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        try {
            return successResponse($currency);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view currency');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCurrencyRequest  $request
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        try {
            $currency->update($request->validated());
            return successResponse($currency);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update currency');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        try {
            if (count($currency->budgets) > 0) {
                throw new Exception('Cannot delete currencies with budgets attached');
            }

            $currency->delete();
            return successResponse($currency);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete currency');
        }
    }
}
