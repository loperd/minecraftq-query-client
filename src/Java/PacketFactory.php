<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Java\Packet\HandshakePacket;

final class PacketFactory
{
    public static function createHandshakePacket(
        ServerAddress   $serverAddress,
        JavaProtocolVersion $protocol,
        int             $state = HandshakePacket::STATUS
    ): HandshakePacket {
        $packet = new HandshakePacket();
        $packet->port = $serverAddress->port;
        $packet->host = $serverAddress->address;
        $packet->state = $state;
        $packet->protocol = $protocol;

        return $packet;
    }
}
