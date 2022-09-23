<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Exception;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
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
                Category::whereBudgetId($budget->id)
                        ->orderBy('type', 'asc')
                        ->orderBy('name', 'asc')
                        ->get()
                        ->toArray()
            );
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view all category');
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
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $category = Category::create($data);

            return successResponse($category);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create category');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        try {
            return successResponse($category);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create category');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->validated());

            return successResponse($category);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update category');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        try {
            $transactions = $category->transactions;

            $existing_other_category = Category::whereName('Other')
                                               ->whereType($category->type)
                                               ->whereUserId($category->user_id)
                                               ->whereBudgetId($category->budget_id)
                                               ->first();

            if (!$existing_other_category) {
                $existing_other_category = Category::create([
                    'name' => 'Other',
                    'type' => $category->type,
                    'user_id' => $category->user_id,
                    'budget_id' => $category->budget_id,
                ]);
            }

            if (count($transactions) > 0) {
                $recurring_transaction_ids = [];
                foreach ($transactions as $transaction) {
                    $transaction->category_id = $existing_other_category->id;
                    $transaction->save();
    
                    if ($transaction->recurring_transaction_id && !in_array($transaction->recurring_transaction_id, $recurring_transaction_ids)) {
                        $recurring_transaction_ids[] = $transaction->recurring_transaction_id;
                    }
                }
    
                $recurring_transactions = RecurringTransaction::whereIn('id', $recurring_transaction_ids)->get();
    
                foreach ($recurring_transactions as $transaction) {
                    $transaction->category_id = $existing_other_category->id;
                    $transaction->save();
                }
            }

            $category->delete();

            return successResponse($category);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete category');
        }
    }
}
