<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Exception\SocketConnectionException;
use Socket\Raw as Socket;

final class BothClient implements MinecraftClient
{
    private ?Query\QueryMinecraftClient $queryClient = null;
    private ?Ping\TCPMinecraftClient $tcpClient = null;

    public function __construct(
        private readonly ServerAddress $address,
        private readonly float $timeout = 1.5
    ) {
        try {
            $this->queryClient = MinecraftClientFactory::createQueryClient($this->address, $this->timeout);
        } catch (SocketConnectionException) {
            // ignored
        }

        $this->tcpClient = MinecraftClientFactory::createTCPClient($this->address, $this->timeout);
    }

    public function getStats(): ServerStatsResponse
    {
        try {
            $queryStats = $this->queryClient?->getStats();
        } catch (\RuntimeException|Socket\Exception) {
            $queryStats = null;
        }

        $pingStats = $this->tcpClient->getStats();
        return $this->mergeStats($pingStats, $queryStats);
    }

    public function close(): void
    {
        $this->queryClient?->close();
        $this->tcpClient->close();
    }

    private function mergeStats(ServerStatsResponse $pingResponse, ?ServerStatsResponse $queryResponse): ServerStatsResponse
    {
        $response = new ServerStatsResponse();
        $response->version = $pingResponse->version;
        $response->protocol = $pingResponse->protocol;
        $response->plugins = $queryResponse?->plugins ?? $pingResponse->plugins;
        $response->map = $queryResponse?->map ?? $pingResponse->map;
        $response->numPlayers = $pingResponse->numPlayers;
        $response->maxPlayers = $pingResponse->maxPlayers;
        $response->port = $queryResponse?->port ?? $pingResponse->port;
        $response->host = $queryResponse?->host ?? $pingResponse->host;
        $response->players = $queryResponse?->players ?? $pingResponse->players;
        $response->motd = $pingResponse->motd ?? $queryResponse?->motd;

        return $response;
    }
}