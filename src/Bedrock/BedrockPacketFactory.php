<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Bedrock;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;

final class BedrockPacketFactory
{
    public static function createUnconnectedPingPacket(ProtocolVersion $protocol): UnconnectedPingPacket
    {
        if (!($protocol instanceof BedrockProtocolVersion)) {
            throw new \InvalidArgumentException(
                sprintf(
                'Protocol object is not instance of "%s" class.',
                BedrockProtocolVersion::class
            )
            );
        }

        $packet = new UnconnectedPingPacket();
        $packet->protocol = $protocol;

        return $packet;
    }
}
