<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;

class TestPacket implements Packet
{
    public bool $readed;

    public function getPacketId(): int
    {
        return 0x00;
    }

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        $this->readed = true;
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeInt($protocol->getProtocolValue());
    }
}
