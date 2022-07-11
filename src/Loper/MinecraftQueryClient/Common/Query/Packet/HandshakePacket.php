<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common\Query\Packet;

use Loper\MinecraftQueryClient\Common\Query\ChallengeToken;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

final class HandshakePacket implements Packet
{
    public const PACKET_ID = 0x09;

    public int $sessionId;

    public ChallengeToken $challengeToken;

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        $this->sessionId = $is->readInt();

        // remove null byte
        $is->readByte();

        $token = (int) $is->readFullData()->bytes();

        $this->challengeToken = new ChallengeToken($token, $this->sessionId);
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeInt($this->sessionId);
    }

    public function getPacketId(): int
    {
        return self::PACKET_ID;
    }
}
