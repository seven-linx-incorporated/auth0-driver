<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use SevenLinX\Auth\Auth0\Contracts\ConfigContract;
use SevenLinX\Auth\Auth0\Contracts\RepositoryContract;
use Throwable;

use function array_merge;
use function implode;
use function sprintf;

final class Repository implements RepositoryContract
{
    public function __construct(
        private Auth0 $sdk,
        private Authentication $authentication,
        private ConfigContract $config,
        private ?Request $request = null
    ) {
    }

    /**
     * @param  string      $token
     * @param  array|null  $options
     *
     * @return array<string, mixed>
     * @throws \Auth0\SDK\Exception\InvalidTokenException
     */
    public function decode(string $token, ?array $options = null): array
    {
        $verifier = new TokenVerifier(
            sprintf('https://%s/', $this->config->getDomain()),
            $this->config->getAudienceIdentifier(),
            $this->config->getSupportedAlgorithms()[0] ?? 'RS256',
        );

        return $verifier->getUser($token, $options);
    }

    public function getUser(?string $token = null): ?array
    {
        $token = $token ?? $this->request->bearerToken();

        try {
            return $this->authentication->userinfo((string) $token);
        } catch (Throwable $throwable) {
            return null;
        }
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

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}