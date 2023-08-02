<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common\Query;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Common\Query\Packet\BasicStatPacket;
use Loper\MinecraftQueryClient\Common\Query\Packet\FullStatPacket;
use Loper\MinecraftQueryClient\Common\MinecraftServerPing;
use Loper\MinecraftQueryClient\Common\ServerPingResult;
use Socket\Raw as Socket;

final class QueryServerPing implements MinecraftServerPing
{
    public function __construct(private readonly ProtocolVersion $protocol, private readonly float $timeout)
    {
    }

    public function ping(ServerAddress $serverAddress): ServerPingResult
    {
        try {
            $client = new QueryMinecraftClient($serverAddress, $this->timeout);
            $challengeToken = $client->getChallengeToken($this->protocol);

            $basicStatPacket = $client->getBasicStat($challengeToken, $this->protocol);
            $fullStatPacket = $client->getFullStat($challengeToken, $this->protocol);

            return $this->createResponse($fullStatPacket, $basicStatPacket);
        } catch (\RuntimeException|Socket\Exception) {
            return new ServerPingResult();
        }
    }

    private function createResponse(FullStatPacket $fullStatPacket, BasicStatPacket $basicStatPacket): ServerPingResult
    {
        $response = new ServerPingResult();
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
        $response->rawMotd = $basicStatPacket->rawMotd;

        return $response;
    }
}
