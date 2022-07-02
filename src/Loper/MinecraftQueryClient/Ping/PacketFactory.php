<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Ping;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Ping\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

final class PacketFactory
{
    public static function createHandshakePacket(
        ServerAddress $serverAddress,
        ProtocolVersion $protocol,
        int $state = HandshakePacket::STATUS
    ): HandshakePacket {
        $packet = new HandshakePacket();
        $packet->port = $serverAddress->port;
        $packet->host = $serverAddress->address;
        $packet->state = $state;
        $packet->protocol = $protocol;

        return $packet;
    }
}
