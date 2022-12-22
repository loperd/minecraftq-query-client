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

        $domainRegEx = '/^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9])|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9]{2,30}\.[a-zA-Z]{2,3})$/';
        $ipRegEx = '/^((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}$/';

        if( 1 !== preg_match($domainRegEx, $this->host) &&
            1 !== preg_match($ipRegEx, $this->host) &&
            1 !== preg_match($ipRegEx, $this->address)
        ) {
            throw new \InvalidArgumentException(sprintf('Invalid server host/address - "%s"', $host));
        }

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
