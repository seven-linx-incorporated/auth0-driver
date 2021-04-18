<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Tests\Unit;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use ErrorException;
use Mockery\MockInterface;
use SevenLinX\Auth\Auth0\Contracts\ConfigContract;
use SevenLinX\Auth\Auth0\Repository;
use SevenLinX\Auth\Auth0\Tests\AbstractTestCase;

/**
 * @covers \SevenLinX\Auth\Auth0\Repository
 */
final class RepositoryTest extends AbstractTestCase
{
    public function testGetUserFailed(): void
    {
        $token = 'token';
        /** @var \Auth0\SDK\API\Authentication $authentication */
        $authentication = $this->mock(Authentication::class, function (MockInterface $mock) use ($token) {
            $mock->shouldReceive('userinfo')
                ->with($token)
                ->andThrow(ErrorException::class);
        });
        /** @var \Auth0\SDK\Auth0 $auth0 */
        $auth0 = $this->mock(Auth0::class);
        /** @var \SevenLinX\Auth\Auth0\Contracts\ConfigContract $config */
        $config = $this->mock(ConfigContract::class);

        $repository = new Repository(
            $auth0,
            $authentication,
            $config
        );

        self::assertNull($repository->getUser('token'));
    }

    public function testGetUserSuccessfully(): void
    {
        $token = 'token';
        /** @var \Auth0\SDK\API\Authentication $authentication */
        $authentication = $this->mock(Authentication::class, function (MockInterface $mock) use ($token) {
            $mock->shouldReceive('userinfo')
                ->with($token)
                ->andReturn([
                    'foo' => 'bar',
                ]);
        });
        /** @var \Auth0\SDK\Auth0 $auth0 */
        $auth0 = $this->mock(Auth0::class);
        /** @var \SevenLinX\Auth\Auth0\Contracts\ConfigContract $config */
        $config = $this->mock(ConfigContract::class);

        $repository = new Repository(
            $auth0,
            $authentication,
            $config
        );

        self::assertNotEmpty($repository->getUser('token'));
    }
}
