<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common\Query\Packet;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Var\VarMotdFilter;
use Loper\MinecraftQueryClient\Var\VarUnsafeFilter;

final class BasicStatPacket implements Packet
{
    public const PACKET_ID = 0x00;

    // Request data
    public int $sessionId;
    public int $challengeToken;

    // Response data
    public string $motd;
    public string $map;
    public int $numPlayers;
    public int $maxPlayers;
    public int $port;
    public string $host;
    public string $rawMotd;

    public function getPacketId(): int
    {
        return self::PACKET_ID;
    }

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        // Remove Type & Session ID
        $is->readBytes(5);

        $rawMotd = VarUnsafeFilter::filter($is->readString()->bytes());
        $this->motd = VarMotdFilter::filter($rawMotd);
        $this->rawMotd = $rawMotd;
        // Remove Game Type
        $is->readString();

        $this->map = VarUnsafeFilter::filter($is->readString()->bytes());
        $this->numPlayers = (int) VarUnsafeFilter::filter($is->readString()->bytes());
        $this->maxPlayers = (int) VarUnsafeFilter::filter($is->readString()->bytes());
        $this->port = $is->readLittleEndianUnsignedShort();
        $this->host = VarUnsafeFilter::filter($is->readString()->bytes());
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeInt($this->sessionId);
        $os->writeInt($this->challengeToken);
    }
}
