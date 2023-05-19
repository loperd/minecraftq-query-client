<?php

declare(strict_types=1);

namespace Loper\Tests;

use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

class TestPacket implements Packet
{
    public bool $testCase;

    public function getPacketId(): int
    {
        return 0x00;
    }

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        $this->testCase = true;
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeInt($protocol->value);
    }
}
