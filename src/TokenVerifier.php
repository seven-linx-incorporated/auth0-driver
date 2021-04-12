<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\SignatureVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier as Auth0TokenVerifier;
use Closure;
use Psr\SimpleCache\CacheInterface;
use SevenLinX\Auth\Auth0\Contracts\TokenVerifierContract;

use function sprintf;

final class TokenVerifier implements TokenVerifierContract
{
    protected Auth0TokenVerifier $verifier;

    public function __construct(
        protected string $tokenIssuer,
        protected string $apiIdentifier,
        protected string $algorithm,
        protected ?string $jwksUri = null,
        protected ?CacheInterface $cacheHandler = null,
        protected ?Closure $signatureResolver = null
    ) {
    }

    /**
     * @throws \Auth0\SDK\Exception\InvalidTokenException
     */
    public function getUser(string $token, ?array $options = null): array
    {
        return $this->getVerifier()->verify($token, $options ?? []);
    }

    public function getVerifier(): Auth0TokenVerifier
    {
        return $this->verifier = new Auth0TokenVerifier(
            $this->tokenIssuer,
            $this->apiIdentifier,
            $this->resolveSignatureVerifier()
        );
    }

    protected function resolveSignatureVerifier(): SignatureVerifier
    {
        if ($this->signatureResolver !== null) {
            return call_user_func($this->signatureResolver, $this);
        }

        return $this->resolveRS256();
    }

    private function resolveRS256(): AsymmetricVerifier
    {
        $fetcher = new JWKFetcher($this->cacheHandler);
        $jwksUri = $this->jwksUri ?? sprintf(
                '%s/.well-known/jwks.json',
                rtrim($this->tokenIssuer, '/')
            );

        return new AsymmetricVerifier($fetcher->getKeys($jwksUri));
    }
}