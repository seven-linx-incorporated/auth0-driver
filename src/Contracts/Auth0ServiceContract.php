<?php
declare(strict_types=1);

namespace SevenLinX\Auth\Auth0\Contracts;

interface Auth0ServiceContract
{
    public function repository(): RepositoryContract;
}