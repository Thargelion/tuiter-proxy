<?php

namespace App\Services;

use App\Models\User;

interface UserCrud
{
    public function readByEmail(string $email): User;
}
