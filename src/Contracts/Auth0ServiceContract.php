<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface Auth0ServiceContract
{
    public function config(): ConfigContract;

    public function repository(): RepositoryContract;

    public function getUser(string $token): Authenticatable;
}