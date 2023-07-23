<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Provider;

use Loper\Minecraft\Protocol\Map\JavaVersionProtocolMap;
use Loper\MinecraftQueryClient\Java\JavaMinecraftClient;
use Loper\MinecraftQueryClient\Java\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\Response\ServerStatisticsResponse;

final class PingServerStatisticsProvider implements ServerStatisticsProvider
{
    public function __construct(public readonly JavaMinecraftClient $client)
    {
    }

    public function getStatistics(): ServerStatisticsResponse
    {
        $packet = $this->client->createHandshakePacket();

        $this->client->sendPacket($packet);

        return $this->createServerStatsResponse($packet);
    }

    private function createServerStatsResponse(HandshakePacket $packet): ServerStatisticsResponse
    {
        $version = JavaVersionProtocolMap::findByProtocol($packet->serverProtocol);

        $response = new ServerStatisticsResponse();
        $response->version = $version;
        $response->protocol = $packet->serverProtocol;
        $response->serverSoftware = $packet->serverSoftware;
        $response->maxPlayers = $packet->maxPlayers;
        $response->numPlayers = $packet->onlinePlayers;
        $response->motd = $packet->motd;
        $response->rawMotd = $packet->rawMotd;
        $response->players = $packet->players;

        return $response;
    }
}
