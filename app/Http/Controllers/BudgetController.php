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
        //
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

            $start_date = Carbon::create($budget->starts_at);
            $every = $budget->time_period_amount ?? 1;
            $time_period = $this->parseTimePeriod($budget->time_period->name);
            $end_date = Carbon::create($budget->starts_at)->add($time_period, $every * $budget->future_intervals)->subDay();

            $periods = CarbonPeriod::create($start_date, "{$every} {$time_period}", $end_date)->toArray();
            foreach ($periods as $period) {
                Interval::create([
                    'budget_id' => $budget->id,
                    'user_id' => Auth::id(),
                    'time_period_id' => $budget->time_period->id,
                    'time_period_amount' => $budget->time_period_amount,
                    'starts_at' => $period->toDateTimeString(),
                    'ends_at' => Carbon::create($period)->add($time_period, $every)->subDay()->toDateTimeString(),
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(Budget $budget)
    {
        //
    }

    protected function parseTimePeriod($time_period): string
    {
        if (in_array($time_period, ['daily', 'day'])) {
            return 'day';
        }
        
        if (in_array($time_period, ['weekly', 'week'])) {
            return 'week';
        }
        
        if (in_array($time_period, ['monthly', 'month'])) {
            return 'month';
        }
        
        if (in_array($time_period, ['quarterly', 'quarter'])) {
            return 'quarter';
        }
        
        if (in_array($time_period, ['yearly', 'year'])) {
            return 'year';
        }
    }
}
