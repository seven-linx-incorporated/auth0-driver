<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Contracts;

use Illuminate\Http\RedirectResponse;

interface RepositoryContract
{
    public function decode(string $token, ?array $options = null): array;

    public function getUser(?string $token = null): ?array;

    public function login(
        array $scopes = [],
        string $responseType = 'code',
        ?string $connection = null,
        ?string $state = null,
        ?array $additionalParams = null
    ): RedirectResponse;
}