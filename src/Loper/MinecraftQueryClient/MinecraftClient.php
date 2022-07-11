<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

interface MinecraftClient
{
    public function sendPacket(Packet $packet);

    public function close(): void;
}
