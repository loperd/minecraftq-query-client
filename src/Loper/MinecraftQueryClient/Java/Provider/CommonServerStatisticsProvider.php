<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Provider;

use Loper\MinecraftQueryClient\Common\Query\QueryMinecraftClient;
use Loper\MinecraftQueryClient\Exception\ClientNotFoundException;
use Loper\MinecraftQueryClient\Java\ServerStatisticsResponse;
use Loper\MinecraftQueryClient\Java\JavaMinecraftClient;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

final class CommonServerStatisticsProvider implements ServerStatisticsProvider
{
    private ?QueryServerStatisticsProvider $queryProvider;
    private ?PingServerStatisticsProvider $pingProvider;

    public function __construct(
        ?JavaMinecraftClient  $javaClient = null,
        ?QueryMinecraftClient $queryClient = null,
    ) {
        if (null === $queryClient && null === $javaClient) {
            throw new ClientNotFoundException();
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
        $response->protocol = $pingResponse?->protocol ?? $queryResponse?->protocol ?? ProtocolVersion::JAVA_1_19_4;
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
}
