<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Provider;

use Loper\MinecraftQueryClient\Common\Query\QueryMinecraftClient;
use Loper\MinecraftQueryClient\Java\ServerStatisticsResponse;
use Loper\MinecraftQueryClient\Java\JavaMinecraftClient;

final class CommonServerStatisticsProvider implements ServerStatisticsProvider
{
    private ?QueryServerStatisticsProvider $queryProvider;
    private ?PingServerStatisticsProvider $pingProvider;

    public function __construct(
        ?JavaMinecraftClient  $javaClient = null,
        ?QueryMinecraftClient $queryClient = null,
    ) {
        if (null === $queryClient && null === $javaClient) {
            throw new \InvalidArgumentException('One or both of the clients should be passed.');
        }

        $this->queryProvider = null === $queryClient ? null : new QueryServerStatisticsProvider($queryClient);
        $this->pingProvider = null === $javaClient ? null : new PingServerStatisticsProvider($javaClient);
    }

    public function getStatistics(): ServerStatisticsResponse
    {
        $queryStats = $this->queryProvider?->getStatistics();
        $pingStats = $this->pingProvider?->getStatistics();

        return $this->mergeStats($pingStats, $queryStats);
    }

    private function mergeStats(?ServerStatisticsResponse $pingResponse, ?ServerStatisticsResponse $queryResponse): ServerStatisticsResponse
    {
        $response = new ServerStatisticsResponse();
        $response->version = $pingResponse?->version ?? $queryResponse?->version;
        $response->protocol = $pingResponse?->protocol ?? $queryResponse?->protocol;
        $response->plugins = $queryResponse?->plugins ?? $pingResponse?->plugins;
        $response->map = $queryResponse?->map ?? $pingResponse?->map;
        $response->numPlayers = $pingResponse?->numPlayers ?? $queryResponse?->numPlayers;
        $response->maxPlayers = $pingResponse?->maxPlayers ?? $queryResponse?->maxPlayers;
        $response->port = $queryResponse?->port ?? $pingResponse?->port;
        $response->host = $queryResponse?->host ?? $pingResponse?->host;
        $response->players = $queryResponse?->players ?? $pingResponse?->players;
        $response->motd = $pingResponse->motd ?? $queryResponse?->motd;

        return $response;
    }
}