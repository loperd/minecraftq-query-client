<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

interface Packet
{
    public function getPacketId(): int;

    public function read(InputStream $is, ProtocolVersion $protocol): void;

    public function write(OutputStream $os, ProtocolVersion $protocol): void;
}
