<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Address;

use Loper\MinecraftQueryClient\Exception\ServerAddressResolveException;

final class ServerAddress
{
    public const DOMAIN_REGEX = '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/';
    public const IP_REGEX = '/^((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}$/';
    public const DEFAULT_SERVER_PORT = 25565;

    public readonly int $port;
    public readonly string $address;

    public function __construct(
        public readonly ServerAddressType $type,
        public readonly string $host,
        string $address,
        ?int $port = null,
    ) {
        if (1 !== preg_match(self::IP_REGEX, $address)) {
            throw new ServerAddressResolveException($address);
        }

        $this->address = $type === ServerAddressType::Shared
            ? $host
            : $address;
        $this->port = $port ?? self::DEFAULT_SERVER_PORT;
    }

    public function format(): string
    {
        return \sprintf('%s:%d', $this->address, $this->port);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
