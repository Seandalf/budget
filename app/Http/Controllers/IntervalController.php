<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Exception;
use Illuminate\Http\Request;

class IntervalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Budget $budget)
    {
        $this->authorize('view', $budget);

        try {
            $intervals = $budget->intervals()->orderBy('starts_at', 'asc')->get();
    
            $final = [];
            $i = 0;
            foreach ($intervals as $interval) {
                $arr = $interval->toArray();
                $arr['category_breakdown'] = $interval->transactionsByCategory();
                $arr['statistics'] = $interval->statistics($arr['category_breakdown']);
                $arr['statistics']['is_first'] = $i === 0 ? true : false;

                $final[] = $arr;
                $i++;
            }
    
            return successResponse($final);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not get budget data');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
