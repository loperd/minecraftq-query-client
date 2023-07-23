<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Bedrock;

use Loper\Minecraft\Protocol\Map\BedrockVersionProtocolMap;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;
use Loper\MinecraftQueryClient\Response\ServerStatisticsResponse;
use Loper\MinecraftQueryClient\Var\VarUnsafeFilter;

final class PingServerStatisticsProvider
{
    public function __construct(public readonly BedrockMinecraftClient $client)
    {
    }

    public function getStatistics(): ServerStatisticsResponse
    {
        $packet = $this->client->createUnconnectedPingPacket();

        $this->client->sendPacket($packet);

        return $this->createServerStatsResponse($packet);
    }

    private function createServerStatsResponse(UnconnectedPingPacket $packet): ServerStatisticsResponse
    {
        $version = BedrockVersionProtocolMap::findByProtocol($packet->protocol);

        $response = new ServerStatisticsResponse();
        $response->version = $version;
        $response->protocol = $packet->protocol;
        $response->serverSoftware = \sprintf('%s | %s', $packet->gameId, $packet->gameVersion);
        $response->maxPlayers = $packet->maxPlayers;
        $response->numPlayers = $packet->currentPlayers;
        $response->motd = VarUnsafeFilter::filter($packet->description);
        $response->rawMotd = $packet->description;
        $response->players = [];

        return $response;
    }
}
