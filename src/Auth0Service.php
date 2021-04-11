<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Auth0\SDK\Auth0;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use SevenLinX\Auth\Auth0\Contracts\Auth0ServiceContract;

use function array_merge;

/**
 * @mixin \SevenLinX\Auth\Auth0\Repository
 */
final class Auth0Service implements Auth0ServiceContract
{
    /**
     * @var string[]
     */
    private array $defaultSupportedAlgorithms = [
        'RS256',
    ];

    public function __construct(private Request $request, private array $config)
    {
    }

    public function __call($method, $parameters)
    {
        return $this->repository()->$method(...$parameters);
    }

    public function repository(): Repository
    {
        return new Repository(new Auth0(array_merge(
            $this->config,
            [
                'client_id' => $this->getClientID(),
                'client_secret' => $this->getClientSecret(),
                'domain' => $this->getDomain(),
                'redirect_uri' => $this->getRedirectUri(),
                'audience' => $this->getAudienceIdentifier(),
                'guzzle_options' => $this->getClientOptions(),
            ]
        )));
    }

    public function getClientID(): ?string
    {
        return $this->getConfig('clientID');
    }

    public function getConfig(string $key): mixed
    {
        return Arr::dot($this->config)[$key] ?? null;
    }

    public function getClientSecret(): ?string
    {
        return $this->getConfig('clientSecret');
    }

    public function getDomain(): ?string
    {
        return $this->getConfig('domain');
    }

    public function getRedirectUri(): ?string
    {
        return $this->getConfig('redirectUri')
            ?? ($this->request->getSchemeAndHttpHost().'/auth/callback');
    }

    public function getAudienceIdentifier(): ?string
    {
        return $this->getConfig('audienceIdentifier');
    }

    public function getClientOptions(): ?array
    {
        return $this->getConfig('clientOptions') ?? [];
    }

    public function getSupportedAlgorithms(): ?array
    {
        return $this->getConfig('supportedAlgs')
            ?? $this->defaultSupportedAlgorithms;
    }
}