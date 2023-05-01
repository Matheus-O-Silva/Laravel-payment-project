<?php

namespace App\Models\Traits;

use App\Models\User;

trait UserACLTrait
{
    public function hasPermission($id)
    {
        $user = User::find($id);
        $permissions = $user->permissions;

        $permissions = $user->permissions()->pluck('name');


        return $permissions;
    }
}
