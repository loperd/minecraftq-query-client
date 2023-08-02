<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Common\Query\QueryServerPing;
use Loper\MinecraftQueryClient\Common\MinecraftServerPing;
use Loper\MinecraftQueryClient\Common\ServerPingResult;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;

final class JavaAggregatedServerPing implements MinecraftServerPing
{
    public function __construct(private readonly JavaProtocolVersion $protocol, private readonly float $timeout)
    {
    }

    public function ping(ServerAddress $serverAddress): ServerPingResult
    {
        $results = [];

        foreach ([JavaServerPing::class, QueryServerPing::class] as $pingClient) {
            try {
                $results[] = $this->createPingClient($pingClient)->ping($serverAddress);
            } catch (SocketConnectionException) {
                $results[] = null;
            }
        }

        return $this->mergeStats(...$results);
    }

    private function mergeStats(?ServerPingResult $pingResponse, ?ServerPingResult $queryResponse = null): ServerPingResult
    {
        $response = new ServerPingResult();
        $response->version = $pingResponse?->version ?? $queryResponse?->version;
        $response->protocol = $pingResponse?->protocol ?? $queryResponse?->protocol ?? JavaProtocolVersion::JAVA_1_20_1;
        $response->plugins = $queryResponse?->plugins ?? $pingResponse?->plugins ?? [];
        $response->map = $queryResponse?->map ?? $pingResponse?->map;
        $response->numPlayers = $pingResponse?->numPlayers ?? $queryResponse?->numPlayers ?? 0;
        $response->maxPlayers = $pingResponse?->maxPlayers ?? $queryResponse?->maxPlayers ?? 0;
        $response->port = $queryResponse?->port ?? $pingResponse?->port;
        $response->host = $queryResponse?->host ?? $pingResponse?->host;
        $response->players = $queryResponse?->players ?? $pingResponse?->players ?? [];
        $response->motd = $pingResponse->motd ?? $queryResponse?->motd;

        return $response;
    }

    /**
     * @param class-string<MinecraftServerPing> $pingClient
     *
     * @return MinecraftServerPing
     */
    public function createPingClient(string $pingClient): MinecraftServerPing
    {
        return new $pingClient($this->protocol, $this->timeout);
    }
}
