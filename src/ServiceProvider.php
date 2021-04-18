<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use SevenLinX\Auth\Auth0\Contracts\Auth0ServiceContract;
use SevenLinX\Auth\Auth0\Contracts\ConfigContract;
use SevenLinX\Auth\Auth0\Contracts\RepositoryContract;

final class ServiceProvider extends IlluminateServiceProvider
{
    public function boot(): void
    {
        Auth::extend(Auth0Service::AUTH_DRIVER_NAME, function ($app) {
            /** @var \SevenLinX\Auth\Auth0\Auth0Service $service */
            $service = $app->make(Auth0Service::class);
            /** @var \SevenLinX\Auth\Auth0\Repository $repository */
            $repository = $service->repository();

            return new Auth0Guard($app->make(Gate::class), $repository, $app['request']);
        });
    }

    public function register(): void
    {
        $this->app->bind(Auth0ServiceContract::class, function (Application $app) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app['config'];

            return new Auth0Service(
                $app[Gate::class],
                $app['request'],
                $config->get('auth.guards.'.Auth0Service::AUTH_DRIVER_NAME)
            );
        });
        $this->app->bind(Auth0Service::class, Auth0ServiceContract::class);
        $this->app->bind(RepositoryContract::class, function (Application $app) {
            /** @var \SevenLinX\Auth\Auth0\Contracts\Auth0ServiceContract $auth0Service */
            $auth0Service = $app->make(Auth0ServiceContract::class);

            return $auth0Service->repository();
        });
        $this->app->bind(ConfigContract::class, function (Application $app) {
            /** @var \SevenLinX\Auth\Auth0\Contracts\Auth0ServiceContract $auth0Service */
            $auth0Service = $app->make(Auth0ServiceContract::class);

            return $auth0Service->config();
        });
    }
}