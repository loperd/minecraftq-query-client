<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Address;

final class ServerAddressResolver
{
    private const MINECRAFT_SRV_RECORD = '_minecraft._tcp.%s';

    public static function resolve(string $host, ?int $port = null): ServerAddress
    {
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
