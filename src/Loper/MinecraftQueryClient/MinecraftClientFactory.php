<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Exception\SocketConnectionException;
use Loper\MinecraftQueryClient\Ping\TCPMinecraftQueryClient;
use Loper\MinecraftQueryClient\Query\UDPMinecraftQueryClient;

final class MinecraftClientFactory
{
    public static function createClient(ServerAddress $serverAddress, float $timeout = 1.5): MinecraftQueryClient
    {
        try {
            return self::createUDPQueryClient($serverAddress, $timeout);
        } catch (SocketConnectionException) {
            return self::createTCPQueryClient($serverAddress, $timeout);
        }
    }

    public static function createUDPQueryClient(ServerAddress $serverAddress, float $timeout): UDPMinecraftQueryClient
    {
        return new UDPMinecraftQueryClient($serverAddress, $timeout);
    }

    public static function createTCPQueryClient(ServerAddress $serverAddress, float $timeout): TCPMinecraftQueryClient
    {
        return new TCPMinecraftQueryClient($serverAddress, $timeout);
    }

    public static function createBothQueryClient(ServerAddress $address, float $timeout): BothQueryClient
    {
        return new BothQueryClient($address, $timeout);
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }
}
