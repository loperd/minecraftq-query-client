<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\Minecraft\Protocol\ProtocolVersion;

interface MinecraftClient
{
    public function sendPacket(Packet $packet, ProtocolVersion $protocol): void;

    public function close(): void;
}
