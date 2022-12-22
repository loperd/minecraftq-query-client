<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Address;

final class ServerAddressResolver
{
    private const MINECRAFT_SRV_RECORD = '_minecraft._tcp.%s';

    public static function resolve(string $host, ?int $port = null): ServerAddress
    {
        $domainRegEx = '/^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9])|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9]{2,30}\.[a-zA-Z]{2,3})$/';
        $ipRegEx = '/^((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}$/';

        if(1 !== preg_match($domainRegEx, $host) && 1 !== preg_match($ipRegEx, $host)) {
            throw new \InvalidArgumentException(sprintf('The host "%s" can`t have spaces', $host));
        }

        if (false !== \ip2long($host)) {
            return new ServerAddress($host, $host, $port);
        }

        $record = @\dns_get_record(\sprintf(self::MINECRAFT_SRV_RECORD, $host), DNS_SRV);

        if (false === $record || 0 === \count($record)) {
            return new ServerAddress($host, self::resolveIpAddress($host), $port);
        }

        $address = self::resolveIpAddress($record[0]['target']);
        $port = $record[0]['port'] ?? $port;

        return new ServerAddress($host, $address, $port);
    }

    private static function resolveIpAddress(string $host): string
    {
        return \gethostbyname($host);
    }
}
