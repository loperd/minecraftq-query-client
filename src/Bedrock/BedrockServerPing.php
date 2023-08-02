<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Bedrock;

use Loper\Minecraft\Protocol\Map\BedrockVersionProtocolMap;
use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;
use Loper\MinecraftQueryClient\Common\MinecraftServerPing;
use Loper\MinecraftQueryClient\Common\ServerPingResult;
use Loper\MinecraftQueryClient\Var\VarUnsafeFilter;

final class BedrockServerPing implements MinecraftServerPing
{
    public function __construct(private readonly BedrockProtocolVersion $protocol, private readonly float $timeout)
    {
    }

    public function ping(ServerAddress $serverAddress): ServerPingResult
    {
        $client = new BedrockMinecraftClient($serverAddress, $this->timeout);

        $packet = $client->createUnconnectedPingPacket($this->protocol);
        $client->sendPacket($packet, $this->protocol);

        return $this->createServerPingResult($packet);
    }

    private function createServerPingResult(UnconnectedPingPacket $packet): ServerPingResult
    {
        $version = BedrockVersionProtocolMap::findByProtocol($packet->protocol);

        $response = new ServerPingResult();
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
