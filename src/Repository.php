<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Auth0\SDK\Auth0;
use Illuminate\Http\RedirectResponse;

use function array_merge;
use function implode;

final class Repository
{
    public function __construct(private Auth0 $sdk)
    {
    }

    public function login(
        array $scopes = [],
        string $responseType = 'code',
        ?string $connection = null,
        ?string $state = null,
        ?array $additionalParams = null
    ): RedirectResponse {
        $scopes = empty($scopes) === true ? ['openid', 'profile', 'email'] : $scopes;
        $params = [
            'response_type' => $responseType,
            'scope' => implode(' ', $scopes),
        ];
        if ($connection !== null) {
            $params['connection'] = $connection;
        }
        if ($state !== null) {
            $params['state'] = $state;
        }
        $params = array_merge($additionalParams ?? [], $params);

        return new RedirectResponse($this->sdk->getLoginUrl($params));
    }
}