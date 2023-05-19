<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

class ServerAddressResolveException extends MinecraftQueryException
{
    public function __construct(public readonly string $address)
    {
        parent::__construct('Cannot resolve address.');
    }
}
