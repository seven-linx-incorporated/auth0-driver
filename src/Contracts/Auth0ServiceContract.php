<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Contracts;

interface Auth0ServiceContract
{
    public function getAudienceIdentifier(): ?string;

    public function getConfig(string $key): mixed;

    public function getClientID(): ?string;

    public function getClientOptions(): ?array;

    public function getClientSecret(): ?string;

    public function getDomain(): ?string;

    public function getRedirectUri(): ?string;
}