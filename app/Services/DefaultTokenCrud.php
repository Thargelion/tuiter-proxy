<?php

namespace App\Services;

use App\Models\ApplicationToken;
use App\Models\User;

class DefaultTokenCrud implements TokenCrud
{
    private TokenGenerator $tokenGenerator;
    private UserCrud $userCrud;

    public function __construct(TokenGenerator $tokenGenerator, UserCrud $userCrud)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->userCrud = $userCrud;
    }

    public function createAndStore(string $userEmail): void
    {
        $user = $this->userCrud->readByEmail($userEmail);
        /** @var ApplicationToken $token */
        $token = $this->tokenGenerator->generateApplicationToken($userEmail);
        $this->store($token, $user);
    }

    public
    function store(ApplicationToken $token, User $user): void
    {
        $token->user()->associate($user);
        $token->save();
    }

    public
    function readById($id): ApplicationToken
    {
        return ApplicationToken::find($id);
    }

    public
    function deleteById($id): void
    {
        $token = ApplicationToken::find($id);
        $token->delete();
    }

    public
    function findByToken(string $token): ApplicationToken
    {
        return ApplicationToken::where('token', $token)->firstOrFail();
    }
}
