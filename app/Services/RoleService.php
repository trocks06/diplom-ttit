<?php

namespace App\Services;

use App\Models\Role;

class RoleService extends BaseService
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function canBeDeleted(Role $role): bool
    {
        return $role->users()->count() === 0;
    }
}
