<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use SevenLinX\Auth\Auth0\Constants\Auth0Keys;
use SevenLinX\Auth\Auth0\Contracts\Auth0ServiceContract;
use SevenLinX\Auth\Auth0\Contracts\ConfigContract;
use SevenLinX\Auth\Auth0\Contracts\RepositoryContract;
use SevenLinX\Auth\Auth0\DTO\Config;

use function array_merge;

/**
 * @mixin \SevenLinX\Auth\Auth0\Repository
 */
final class Auth0Service implements Auth0ServiceContract
{
    /**
     * @var string
     */
    public const AUTH_DRIVER_NAME = 'sevenlinx-auth0';

    private ConfigContract $config;

    public function __construct(private Gate $gate, private Request $request, private array $configArray)
    {
        $this->config = new Config($this->configArray);
    }

    public function __call(string $method, array $parameters)
    {
        return $this->repository()->$method(...$parameters);
    }

    public function config(): ConfigContract
    {
        return $this->config;
    }

    /**
     * @throws \Auth0\SDK\Exception\CoreException
     */
    public function getUser(string $token): Authenticatable
    {
        return new Auth0User($this->gate, $this->repository()->getUser($token), $token);
    }

    /**
     * @throws \Auth0\SDK\Exception\CoreException
     */
    public function repository(): RepositoryContract
    {
        $auth0 = new Auth0(array_merge(
            $this->config->toArray(),
            [
                Auth0Keys::CLIENT_ID => $this->config->getClientID(),
                Auth0Keys::CLIENT_SECRET => $this->config->getClientSecret(),
                Auth0Keys::DOMAIN => $this->config->getDomain(),
                Auth0Keys::REDIRECT_URI => $this->config->getRedirectUri(),
                Auth0Keys::AUDIENCE => $this->config->getAudienceIdentifier(),
                Auth0Keys::CLIENT_OPTIONS => $this->config->getClientOptions(),
            ]
        ));
        $authentication = new Authentication(
            $this->config->getDomain(),
            $this->config->getClientID(),
            $this->config->getClientSecret(),
            $this->config->getAudienceIdentifier(),
            guzzleOptions: $this->config->getClientOptions() ?? []
        );

        return new Repository(
            $auth0,
            $authentication,
            $this->config,
            $this->request
        );
    }
}