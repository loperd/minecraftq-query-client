<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Query\Packet;

use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

final class BasicStatPacket implements Packet
{
    public const PACKET_ID = 0x00;

    public int $sessionId;
    public int $challengeToken;

    // Response data
    public string $motd;
    public string $map;
    public int $numPlayers;
    public int $maxPlayers;
    public int $port;
    public string $host;

    public function getPacketId(): int
    {
        return self::PACKET_ID;
    }

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        // Remove Type & Session ID
        $is->readBytes(5);

        $this->motd = $is->readString()->bytes();
        // Remove Game Type
        $is->readString();

        $this->map = $is->readString()->bytes();
        $this->numPlayers = (int) $is->readString()->bytes();
        $this->maxPlayers = (int) $is->readString()->bytes();
        $this->port = (int) $is->readString()->bytes();
        $this->host = $is->readString()->bytes();
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeInt($this->sessionId);
        $os->writeInt($this->challengeToken);
    }
}
