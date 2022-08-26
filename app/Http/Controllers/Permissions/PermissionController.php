<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\PermissionRequest;
use App\Models\Permissions\Permission;
use Exception;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        try {
            $permission = Permission::create($request->validated());

            return successResponse($permission);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create permission');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        try {
            return successResponse($permission);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not view permission');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        try {
            $permission->update($request->validated());

            return successResponse($permission);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update permission');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();

            return successResponse($permission);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete permission');
        }
    }
}
