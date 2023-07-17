#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: JavaProtocolVersion::JAVA_1_7_1,
        timeout: 1.5
    );
    $javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

    $provider = $javaMinecraftProviderFactory->createPingStatisticsProvider();

    var_dump($provider->getStatistics());
};
