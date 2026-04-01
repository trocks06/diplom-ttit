<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        return Role::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return $role;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        return $role;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json([
            "message" => "Роль успешно удалена"
        ]);
    }
}
