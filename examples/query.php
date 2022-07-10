<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

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
$client = MinecraftClientFactory::createQueryClient($address, 1.5);

var_dump($client->getStats());
