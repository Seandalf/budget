<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Budget;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Interval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Budget::class, 'budget');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return successResponse(Budget::all());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all budget');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBudgetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBudgetRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $budget = Budget::create($data);

            $periods = $budget->intervalPeriods();
            foreach ($periods as $period) {
                Interval::create([
                    'budget_id' => $budget->id,
                    'user_id' => Auth::id(),
                    'time_period_id' => $budget->time_period->id,
                    'time_period_amount' => $budget->time_period_amount,
                    'starts_at' => $period->toDateTimeString(),
                    'ends_at' => Carbon::create($period)
                                       ->add(parseTimePeriod($budget->time_period->name), $budget->time_period_amount ?? 1)
                                       ->subDay()
                                       ->toDateTimeString(),
                ]);
            }

            return successResponse($budget);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create budget');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function show(Budget $budget)
    {
        try {
            return successResponse($budget);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view budget');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function edit(Budget $budget)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBudgetRequest  $request
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        try {
            $data = $request->validated();
            $updating_total_intervals = $data['future_intervals'] !== $budget->future_intervals;

            $budget->update($data);

            if ($updating_total_intervals) {
                $intervals = Interval::whereUserId(Auth::id())
                                     ->whereBudgetId($budget->id)
                                     ->whereFinal(false)
                                     ->orderBy('starts_at')
                                     ->get();

                if (count($intervals) > $budget->future_intervals) {
                    $i = count($intervals) - 1;

                    do {
                        $intervals[$i]->delete();
                        $i--;
                    } while (count($intervals) > $budget->future_intervals);
                }

                if (count($intervals) < $budget->future_intervals) {
                    $periods = $budget->intervalPeriods();
                    $intervals = $budget->intervals->toArray();

                    foreach ($periods as $period) {
                        $period_date = Carbon::create($period)->startOfDay();

                        if (!array_search($period_date, array_column($intervals, 'starts_at'))) {
                            Interval::create([
                                'budget_id' => $budget->id,
                                'user_id' => Auth::id(),
                                'time_period_id' => $budget->time_period->id,
                                'time_period_amount' => $budget->time_period_amount,
                                'starts_at' => $period->toDateTimeString(),
                                'ends_at' => Carbon::create($period)
                                                   ->add(parseTimePeriod($budget->time_period->name), $budget->time_period_amount ?? 1)
                                                   ->subDay()
                                                   ->toDateTimeString(),
                            ]);
                        }
                    }
                    
                    $budget->recalculateBalances();
                }
            }

            return successResponse($budget);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update budget');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(Budget $budget)
    {
        try {
            $budget->delete();
            return successResponse($budget);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete budget');
        }
    }
}
