<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;

interface Packet
{
    public function getPacketId(): int;

    public function read(InputStream $is, ProtocolVersion $protocol): void;

    public function write(OutputStream $os, ProtocolVersion $protocol): void;
}
