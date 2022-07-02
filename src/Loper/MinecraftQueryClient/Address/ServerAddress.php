<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Address;

final class ServerAddress
{
    public const DEFAULT_SERVER_PORT = 25565;
    public int $port;

    public function __construct(
        public string $host,
        public string $address,
        ?int $port = null
    ) {
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
