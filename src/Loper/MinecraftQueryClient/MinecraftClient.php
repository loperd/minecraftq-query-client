<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

interface MinecraftClient
{
    public function sendPacket(Packet $packet): void;

    public function close(): void;
}
