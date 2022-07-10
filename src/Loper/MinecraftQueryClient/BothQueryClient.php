<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Exception\SocketConnectionException;
use Socket\Raw as Socket;

final class BothQueryClient implements MinecraftQueryClient
{
    private ?Query\UDPMinecraftQueryClient $udpQueryClient = null;
    private ?Ping\TCPMinecraftQueryClient $tcpQueryClient = null;

    public function __construct(
        private readonly ServerAddress $address,
        private readonly float $timeout = 1.5
    ) {
        try {
            $this->udpQueryClient = MinecraftClientFactory::createUDPQueryClient($this->address, $this->timeout);
        } catch (SocketConnectionException) {
            // ignored
        }

        $this->tcpQueryClient = MinecraftClientFactory::createTCPQueryClient($this->address, $this->timeout);
    }

    public function getStats(): ServerStatsResponse
    {
        try {
            $udpStats = $this->udpQueryClient?->getStats();
        } catch (\RuntimeException|Socket\Exception) {
            $udpStats = null;
        }

        $tcpStats = $this->tcpQueryClient->getStats();
        return $this->mergeStats($tcpStats, $udpStats);
    }

    public function close(): void
    {
        $this->udpQueryClient?->close();
        $this->tcpQueryClient->close();
    }

    private function mergeStats(ServerStatsResponse $tcpResponse, ?ServerStatsResponse $udpResponse): ServerStatsResponse
    {
        $response = new ServerStatsResponse();
        $response->version = $tcpResponse->version;
        $response->protocol = $tcpResponse->protocol;
        $response->plugins = $udpResponse?->plugins ?? $tcpResponse->plugins;
        $response->map = $udpResponse?->map ?? $tcpResponse->map;
        $response->numPlayers = $tcpResponse->numPlayers;
        $response->maxPlayers = $tcpResponse->maxPlayers;
        $response->port = $udpResponse?->port ?? $tcpResponse->port;
        $response->host = $udpResponse?->host ?? $tcpResponse->host;
        $response->players = $udpResponse?->players ?? $tcpResponse->players;
        $response->motd = $tcpResponse->motd ?? $udpResponse?->motd;

        return $response;
    }
}