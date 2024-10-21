<?php

namespace App\Services;

use App\Models\ApplicationToken;

class DefaultTokenGenerator implements TokenGenerator
{
    public function generateApplicationToken(string $email): ApplicationToken
    {
        $token = new ApplicationToken();
        $token->token = bin2hex(random_bytes(32));
        $token->expires_at = now()->addDays(365);

        return $token;
    }

}
