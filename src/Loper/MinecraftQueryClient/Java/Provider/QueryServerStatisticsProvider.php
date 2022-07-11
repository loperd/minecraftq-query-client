<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Provider;

use Loper\MinecraftQueryClient\Common\Query\Packet\BasicStatPacket;
use Loper\MinecraftQueryClient\Common\Query\Packet\FullStatPacket;
use Loper\MinecraftQueryClient\Common\Query\QueryMinecraftClient;
use Loper\MinecraftQueryClient\Java\ServerStatisticsResponse;
use Socket\Raw as Socket;

final class QueryServerStatisticsProvider implements ServerStatisticsProvider
{
    public function __construct(private readonly QueryMinecraftClient $client) {
    }

    public function getStatistics(): ServerStatisticsResponse
    {
        try {
            $challengeToken = $this->client->getChallengeToken();

            $basicStatPacket = $this->client->getBasicStat($challengeToken);
            $fullStatPacket = $this->client->getFullStat($challengeToken);

            return $this->createResponse($fullStatPacket, $basicStatPacket);
        } catch (\RuntimeException|Socket\Exception) {
            return new ServerStatisticsResponse();
        }
    }

    private function createResponse(FullStatPacket $fullStatPacket, BasicStatPacket $basicStatPacket): ServerStatisticsResponse
    {
        $response = new ServerStatisticsResponse();
        $response->version = $fullStatPacket->version;
        $response->protocol = $fullStatPacket->serverProtocol;
        $response->plugins = $fullStatPacket->plugins;
        $response->map = $fullStatPacket->map;
        $response->numPlayers = $fullStatPacket->numPlayers;
        $response->maxPlayers = $fullStatPacket->maxPlayers;
        $response->port = $fullStatPacket->port;
        $response->host = $fullStatPacket->host;
        $response->players = $fullStatPacket->players;
        $response->motd = $basicStatPacket->motd;

        return $response;
    }

    public function __destruct()
    {
        $this->client->close();
    }
}