<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->roleService->getAll();
        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());
        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $updatedRole = $this->roleService->update($role->id, $request->validated());
        return new RoleResource($updatedRole);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!$this->roleService->canBeDeleted($role)) {
            return response()->json(['message' => 'Нельзя удалить роль, у которой есть пользователи.'], 400);
        }
        $this->roleService->delete($role->id);
        return response()->json([
            "message" => "Роль успешно удалена."
        ]);
    }
}
