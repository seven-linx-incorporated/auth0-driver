<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    public function __construct(private Request $request, private array $configArray)
    {
        $this->config = new Config($this->configArray);
    }

    public function __call($method, $parameters)
    {
        return $this->repository()->$method(...$parameters);
    }

    public function repository(): RepositoryContract
    {
        $auth0 = new Auth0(array_merge(
            $this->config->toArray(),
            [
                'client_id' => $this->config->getClientID(),
                'client_secret' => $this->config->getClientSecret(),
                'domain' => $this->config->getDomain(),
                'redirect_uri' => $this->config->getRedirectUri(),
                'audience' => $this->config->getAudienceIdentifier(),
                'guzzle_options' => $this->config->getClientOptions(),
            ]
        ));
        $authentication = new Authentication(
            $this->config->getDomain(),
            $this->config->getClientID(),
            $this->config->getClientSecret(),
            $this->config->getAudienceIdentifier(),
            guzzleOptions: $this->config->getClientOptions() ?? []
        );

        return new Repository($auth0, $authentication, $this->config, $this->request);
    }
}