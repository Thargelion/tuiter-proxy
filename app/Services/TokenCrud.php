<?php

namespace App\Services;

use App\Models\ApplicationToken;
use App\Models\User;
use Illuminate\Support\Facades\App;

interface TokenCrud
{
    function createAndStore(string $userEmail);

    function store(ApplicationToken $token, User $user);

    function readById($id): ApplicationToken;

    function deleteById($id);

    function findByToken(string $token): ApplicationToken;
}
