<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: ProtocolVersion::JAVA_1_7_2,
        timeout: 1.5
    );
    $javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

    $provider = $javaMinecraftProviderFactory->createQueryStatisticsProvider();

    var_dump($provider->getStatistics());
};
