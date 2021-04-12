<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ConfigContract extends Arrayable
{
    public function getAudienceIdentifier(): ?string;

    public function getClientID(): ?string;

    public function getClientOptions(): ?array;

    public function getClientSecret(): ?string;

    public function getConfig(string $key): mixed;

    public function getDomain(): ?string;

    public function getRedirectUri(): ?string;

    public function getSupportedAlgorithms(): ?array;
}