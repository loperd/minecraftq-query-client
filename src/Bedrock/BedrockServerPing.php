<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Bedrock;

use Loper\Minecraft\Protocol\Map\BedrockVersionProtocolMap;
use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\Minecraft\Protocol\Struct\BedrockServerVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;
use Loper\MinecraftQueryClient\Common\MinecraftServerPing;
use Loper\MinecraftQueryClient\Common\ServerPingResult;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
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
        try {
            $client->sendPacket($packet, $this->protocol);
        } catch (PacketReadException) {
            return $this->createFailedServerPingResult();
        }

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

    private function createFailedServerPingResult(): ServerPingResult
    {
        $response = new ServerPingResult();
        $response->version = BedrockServerVersion::Unknown;
        $response->protocol = BedrockProtocolVersion::Unknown;
        $response->serverSoftware = \sprintf('Unknown');
        $response->maxPlayers = -1;
        $response->numPlayers = -1;
        $response->motd = 'Server does not respond';
        $response->rawMotd = 'Server does not respond';
        $response->players = [];

        return $response;
    }
}
