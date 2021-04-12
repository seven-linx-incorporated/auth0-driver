<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Illuminate\Contracts\Auth\Authenticatable;

use function array_key_exists;

final class Auth0User implements Authenticatable
{
    public function __construct(private array $userInfo, private string $token)
    {
    }

    public function __get($key): mixed
    {
        return $this->userInfo[$key] ?? null;
    }

    public function __isset($key): bool
    {
        return array_key_exists($key, $this->userInfo);
    }

    public function __set($key, $value): void
    {
        // No setter
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifier(): ?string
    {
        return $this->userInfo['sub'] ?? $this->userInfo['user_id'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getAuthPassword(): string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function getRememberToken(): string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function setRememberToken($value): void
    {
        $this->token = $value;
    }

    /**
     * @inheritDoc
     */
    public function getRememberTokenName(): string
    {
        return 'token';
    }
}