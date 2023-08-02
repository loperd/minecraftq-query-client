<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use Loper\Minecraft\Protocol\Map\JavaVersionProtocolMap;
use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Common\MinecraftServerPing;
use Loper\MinecraftQueryClient\Java\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\Common\ServerPingResult;

final class JavaServerPing implements MinecraftServerPing
{
    public function __construct(private readonly JavaProtocolVersion $protocol, private readonly float $timeout)
    {
    }

    public function ping(ServerAddress $serverAddress): ServerPingResult
    {
        $client = new JavaMinecraftClient($serverAddress, $this->timeout);
        $packet = $client->createHandshakePacket($this->protocol);

        $client->sendPacket($packet, $this->protocol);

        return $this->createServerPingResult($packet);
    }

    private function createServerPingResult(HandshakePacket $packet): ServerPingResult
    {
        $version = JavaVersionProtocolMap::findByProtocol($packet->serverProtocol);

        $response = new ServerPingResult();
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
