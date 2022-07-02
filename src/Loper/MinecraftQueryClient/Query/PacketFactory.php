<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Query;

use Loper\MinecraftQueryClient\Query\Packet\BasicStatPacket;
use Loper\MinecraftQueryClient\Query\Packet\FullStatPacket;
use Loper\MinecraftQueryClient\Query\Packet\HandshakePacket;

final class PacketFactory
{
    public static function createHandshakePacket(): HandshakePacket
    {
        $packet = new HandshakePacket();
        $packet->sessionId = \random_int(999, 9999) & 0x0F0F0F0F;

        return $packet;
    }

    public static function createBasicStatPacket(ChallengeToken $challengeToken): BasicStatPacket
    {
        $packet = new BasicStatPacket();
        $packet->sessionId = $challengeToken->sessionId;
        $packet->challengeToken = $challengeToken->token;

        return $packet;
    }

    public static function createFullStatPacket(ChallengeToken $challengeToken): FullStatPacket
    {
        $packet = new FullStatPacket();
        $packet->sessionId = $challengeToken->sessionId;
        $packet->challengeToken = $challengeToken->token;

        return $packet;
    }
}
