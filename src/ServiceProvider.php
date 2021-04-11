<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use SevenLinX\Auth\Auth0\Contracts\Auth0ServiceContract;

final class ServiceProvider extends IlluminateServiceProvider
{
    public function provides(): array
    {
        return [
            Auth0Service::class,
            Auth0ServiceContract::class,
        ];
    }

    public function register(): void
    {
        $this->app->bind(Auth0Service::class, function (Application $app) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app['config'];

            return new Auth0Service($app['request'], $config->get('auth.guards.auth0'));
        });
    }
}