<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
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
    public function index()
    {
        try {
            return successResponse(Category::all());
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
            if (count($category->transactions) > 0 || count($category->gtransactions) > 0 || count($category->rtransactions) > 0) {
                throw new Exception('Cannot delete category when it has existing transactions');
            }

            $category->delete();

            return successResponse($category);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete category');
        }
    }
}
