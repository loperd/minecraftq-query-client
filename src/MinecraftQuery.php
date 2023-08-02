<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Bedrock\BedrockServerPing;
use Loper\MinecraftQueryClient\Common\Query\QueryServerPing;
use Loper\MinecraftQueryClient\Common\ServerPingResult;
use Loper\MinecraftQueryClient\Java\JavaAggregatedServerPing;
use Loper\MinecraftQueryClient\Java\JavaServerPing;

final class MinecraftQuery
{
    public static function bedrockPing(
        string $host,
        int $port = 19132,
        BedrockProtocolVersion $protocol = BedrockProtocolVersion::BEDROCK_1_20_1,
        float $timeout = 1.5
    ): ServerPingResult {
        $address = ServerAddressResolver::resolve($host, $port);

        return (new BedrockServerPing($protocol, $timeout))->ping($address);
    }
    public static function javaPing(
        string $host,
        int $port = 25565,
        JavaProtocolVersion $protocol = JavaProtocolVersion::JAVA_1_20_1,
        float $timeout = 1.5,
    ): ServerPingResult {
        $address = ServerAddressResolver::resolve($host, $port);

        return (new JavaAggregatedServerPing($protocol, $timeout))->ping($address);
    }

    public static function javaQueryPing(
        string $host,
        int $port = 25565,
        JavaProtocolVersion $protocol = JavaProtocolVersion::JAVA_1_20_1,
        float $timeout = 1.5,
    ): ServerPingResult {
        $address = ServerAddressResolver::resolve($host, $port);

        return (new JavaServerPing($protocol, $timeout))->ping($address);
    }

    public static function queryPing(
        string $host,
        int $port = 25565,
        ProtocolVersion $protocol = JavaProtocolVersion::JAVA_1_20_1,
        float $timeout = 1.5,
    ): ServerPingResult {
        $address = ServerAddressResolver::resolve($host, $port);

        return (new QueryServerPing($protocol, $timeout))->ping($address);
    }
}
