<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Address;

use Loper\MinecraftQueryClient\Exception\ServerAddressResolveException;

final class ServerAddressResolver
{
    private const MINECRAFT_SRV_RECORD = '_minecraft._tcp.%s';

    public static function resolve(string $host, ?int $port = null): ServerAddress
    {
        if (
            1 !== preg_match(ServerAddress::DOMAIN_REGEX, $host)
            && 1 !== preg_match(ServerAddress::IP_REGEX, $host)
        ) {
            throw new ServerAddressResolveException($host);
        }

        if (false !== \ip2long($host)) {
            return new ServerAddress(ServerAddressType::Dedicated, $host, $host, $port);
        }

        $record = @\dns_get_record(\sprintf(self::MINECRAFT_SRV_RECORD, $host), DNS_SRV);

        if (false === $record || 0 === \count($record)) {
            return new ServerAddress(ServerAddressType::Shared, $host, self::resolveIpAddress($host), $port);
        }

        $address = self::resolveIpAddress($record[0]['target']);
        $port = $record[0]['port'] ?? $port;

        return new ServerAddress(ServerAddressType::SrvMapped, $host, $address, $port);
    }

    private static function resolveIpAddress(string $host): string
    {
        return \gethostbyname($host);
    }
}
