<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

require_once __DIR__ . '/../vendor/autoload.php';

$host = $argv[1] ?? null;
$port = $argv[2] ?? 25565;

if (!isset($host)) {
    echo PHP_EOL;
    \printf("Usage: php %s <host> <port>\n", $_SERVER['SCRIPT_FILENAME']);
    echo PHP_EOL;
    exit;
}

$address = ServerAddressResolver::resolve($host, $port);

$minecraftClientFactory = new MinecraftClientFactory($address, ProtocolVersion::JAVA_1_7_2, 1.5);
$javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

$provider = $javaMinecraftProviderFactory->createQueryStatisticsProvider();

var_dump($provider->getStatistics());
