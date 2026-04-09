<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $roles = $this->service->getAll();
        return RoleResource::collection($roles);
    }

    public function show(Role $role)
    {
        return new RoleResource($role);
    }
}
