<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permissions\RoleRequest;
use App\Models\Permissions\Role;
use Exception;

class RoleController extends Controller
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
    public function store(RoleRequest $request)
    {
        try {
            $data = $request->validated();
            $role = Role::create($data['name']);

            foreach ($data['permissions'] as $permission) {
                $role->assignPermission($permission['name']);
            }

            return successResponse($role);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not create role');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        try {
            $data = $request->validated();
            $role->update($data['name']);

            foreach ($data['permissions'] as $permission) {
                if ($permission['delete']) {
                    $role->removePermission($permission['name']);
                } else {
                    $role->assignPermission($permission['name']);
                }
            }

            return successResponse($role);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not update role');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();

            return successResponse($role);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 'Could not delete role');
        }
    }
}
