<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Contracts;

use Auth0\SDK\Helpers\Tokens\TokenVerifier;

interface TokenVerifierContract
{
    public function getUser(string $token, ?array $options = null): array;

    public function getVerifier(): TokenVerifier;
}