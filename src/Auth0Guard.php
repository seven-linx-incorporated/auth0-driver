<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Throwable;

use function count;

final class Auth0Guard implements Guard
{
    private ?Authenticatable $user = null;

    public function __construct(private Gate $gate, private Repository $repository, private Request $request)
    {
        $this->repository->setRequest($request);
    }

    /**
     * @inheritDoc
     */
    public function check(): bool
    {
        $user = $this->repository->getUser();

        return $user !== null && count($user) !== 0;
    }

    /**
     * @inheritDoc
     */
    public function guest(): bool
    {
        return $this->check() === false;
    }

    /**
     * @inheritDoc
     */
    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $user = $this->repository->getUser();

        return $this->user = $user !== null
            ? new Auth0User($this->gate, $user, $this->request->bearerToken())
            : null;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        return optional($this->user())->getAuthIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function validate(array $credentials = []): bool
    {
        $token = $credentials['token'] ?? $this->request->bearerToken() ?? null;

        if ($token === null) {
            return false;
        }

        try {
            $decode = $this->repository->decode($token, $credentials['options'] ?? null);

            return count($decode) > 0;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function setUser(Authenticatable $user): void
    {
        $this->user = $user;
    }
}