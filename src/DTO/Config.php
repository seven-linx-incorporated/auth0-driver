<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\DTO;

use Illuminate\Support\Arr;
use SevenLinX\Auth\Auth0\Contracts\ConfigContract;

final class Config implements ConfigContract
{
    /**
     * @var string[]
     */
    private array $defaultSupportedAlgorithms = [
        'RS256',
    ];

    public function __construct(private array $config)
    {
    }

    public function getAudienceIdentifier(): ?string
    {
        return $this->getConfig('audienceIdentifier');
    }

    public function getConfig(string $key): mixed
    {
        return Arr::dot($this->config)[$key] ?? null;
    }

    public function getClientID(): ?string
    {
        return $this->getConfig('clientID');
    }

    public function getClientOptions(): ?array
    {
        return $this->getConfig('clientOptions');
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
        return $this->getConfig('redirectUri');
    }

    public function toArray(): array
    {
        return $this->config;
    }

    public function getSupportedAlgorithms(): ?array
    {
        return $this->getConfig('supportedAlgs')
            ?? $this->defaultSupportedAlgorithms;
    }
}