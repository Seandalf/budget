<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Budget;
use App\Models\Interval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Payee;
use App\Models\TimePeriod;

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
        return Inertia::render('Budgets/viewAll', [
            'budgets' => Auth::user()->budgets()->with('currency', 'time_period')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Budgets/Create', [
            'currencies' => Currency::all(),
            'payees' => Payee::whereUserId(Auth::id())->orWhereNull('user_id')->get(),
            'categories' => Category::whereUserId(Auth::id())->orWhereNull('user_id')->get(),
            'timePeriods' => TimePeriod::all(),
        ]);
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
            $user = Auth::user();
            $data = [
                ...$request->validated(),
                'user_id' => $user->id,
            ];

            $intervals_validation = $this->checkFutureIntervals($user, $data);
            if (!$intervals_validation['valid']) {
                return response()->json([
                    'message' => $intervals_validation['message']
                ])->setStatusCode(422);
            }

            $data['opening_balance'] = $data['opening_balance'] * 100;

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

                        if (! array_search($period_date, array_column($intervals, 'starts_at'))) {
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

    protected function checkFutureIntervals($user, $data)
    {
        if ($user->hasPermission('budget-9')) {
            $time_period = TimePeriod::find($data['time_period_id']);

            switch ($time_period->name) {
                case 'daily':
                    $limit = 273;
                    break;
                case 'weekly':
                    $limit = 39;
                    break;
                case 'monthly':
                    $limit = 9;
                    break;
                    case 'quarterly':
                        $limit = 3;
                        break;
                case 'yearly':
                    $limit = 0;
                    break;
                case 'day':
                    $limit = floor(273 / $data['time_period_amount']);
                    break;
                case 'week':
                    $limit = floor(39 / $data['time_period_amount']);
                    break;
                case 'month':
                    $limit = floor(9 / $data['time_period_amount']);
                    break;
                    case 'quarter':
                        $limit = floor(3 / $data['time_period_amount']);
                        break;
                case 'year':
                    $limit = 0;
                    break;
            }

            $message = 'Cannot go more than 9 months into the future';
        }
        if ($user->hasPermission('budget-12')) {
            $time_period = TimePeriod::find($data['time_period_id']);

            switch ($time_period->name) {
                case 'daily':
                    $limit = 366;
                    break;
                case 'weekly':
                    $limit = 52;
                    break;
                case 'monthly':
                    $limit = 12;
                    break;
                    case 'quarterly':
                        $limit = 4;
                        break;
                case 'yearly':
                    $limit = 1;
                    break;
                case 'day':
                    $limit = floor(366 / $data['time_period_amount']);
                    break;
                case 'week':
                    $limit = floor(52 / $data['time_period_amount']);
                    break;
                case 'month':
                    $limit = floor(12 / $data['time_period_amount']);
                    break;
                    case 'quarter':
                        $limit = floor(4 / $data['time_period_amount']);
                        break;
                case 'year':
                    $limit = 1;
                    break;
            }

            $message = 'Cannot go more than 12 months into the future';
        }
        if ($user->hasPermission('budget-unlimited')) {
            $time_period = TimePeriod::find($data['time_period_id']);

            switch ($time_period->name) {
                case 'daily':
                    $limit = 365 + 366;
                    break;
                case 'weekly':
                    $limit = 104;
                    break;
                case 'monthly':
                    $limit = 24;
                    break;
                    case 'quarterly':
                        $limit = 8;
                        break;
                case 'yearly':
                    $limit = 2;
                    break;
                case 'day':
                    $limit = floor(731 / $data['time_period_amount']);
                    break;
                case 'week':
                    $limit = floor(104 / $data['time_period_amount']);
                    break;
                case 'month':
                    $limit = floor(24 / $data['time_period_amount']);
                    break;
                    case 'quarter':
                        $limit = floor(8 / $data['time_period_amount']);
                        break;
                case 'year':
                    $limit = 2;
                    break;
            }

            $message = 'Cannot go more than 2 years into the future';
        }

        if ($data['future_intervals'] > $limit) {
            return [
                'valid' => false,
                'message' => $message,
            ];
        }

        return [
            'valid' => true,
        ];
    }
}
