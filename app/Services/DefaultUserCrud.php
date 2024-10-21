<?php

namespace App\Services;

use App\Models\User;
use App\Services\UserCrud;

class DefaultUserCrud implements UserCrud
{

    public function readByEmail(string $email): User
    {
        return User::where('email', $email)->first();
    }
}
