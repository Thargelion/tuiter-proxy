<?php

namespace App\Services;

interface TokenGenerator
{
    public function generateApplicationToken(string $email): object;
}
