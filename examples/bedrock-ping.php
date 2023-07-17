#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Bedrock\PingServerStatisticsProvider;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: BedrockProtocolVersion::BEDROCK_1_20_1,
        timeout: 1.5
    );

    $provider = new PingServerStatisticsProvider(
        $minecraftClientFactory->createBedrockClient()
    );

    var_dump($provider->getStatistics());
};
