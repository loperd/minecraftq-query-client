<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Exception\SocketConnectionException;
use Loper\MinecraftQueryClient\Ping\TCPMinecraftClient;
use Loper\MinecraftQueryClient\Query\QueryMinecraftClient;

final class MinecraftClientFactory
{
    public static function createClient(ServerAddress $serverAddress, float $timeout = 1.5): MinecraftClient
    {
        try {
            return self::createQueryClient($serverAddress, $timeout);
        } catch (SocketConnectionException) {
            return self::createTCPClient($serverAddress, $timeout);
        }
    }

    public static function createQueryClient(ServerAddress $serverAddress, float $timeout): QueryMinecraftClient
    {
        return new QueryMinecraftClient($serverAddress, $timeout);
    }

    public static function createTCPClient(ServerAddress $serverAddress, float $timeout): TCPMinecraftClient
    {
        return new TCPMinecraftClient($serverAddress, $timeout);
    }

    public static function createBothQueryClient(ServerAddress $address, float $timeout): BothClient
    {
        return new BothClient($address, $timeout);
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }
}
