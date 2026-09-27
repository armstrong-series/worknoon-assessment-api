<?php

namespace App\Actions\Auth;

use App\Models\User;

class GetAuthenticatedUserAction
{

    public function execute(User $user): User
    {
        return $user->load([
            'role',
            'customer',
        ]);
    }
}
