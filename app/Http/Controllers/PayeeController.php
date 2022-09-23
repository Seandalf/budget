<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Payee;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePayeeRequest;
use App\Http\Requests\UpdatePayeeRequest;

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
    public function index(Budget $budget)
    {
        try {
            return successResponse(
                Payee::whereBudgetId($budget->id)
                     ->orderBy('name', 'asc')
                     ->get()
                     ->toArray()
            );
        } catch (Exception $e) {
            dd($e->getMessage());
            return errorResponse($e->getMessage(), 'Could not view all payees');
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
     * @param  \App\Http\Requests\StorePayeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayeeRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $payee = Payee::create($data);

            return successResponse($payee);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create payee');
        }
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
        try {
            $payee->update($request->validated());

            return successResponse($payee);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update payee');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payee $payee)
    {
        try {
            $payee->delete();

            return successResponse($payee);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete payee');
        }
    }
}
