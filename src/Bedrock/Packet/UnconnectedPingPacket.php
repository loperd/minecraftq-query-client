<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Bedrock\Packet;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use PHPinnacle\Buffer\ByteBuffer;

final class UnconnectedPingPacket implements Packet
{
    public const PACKET_ID = 0x01;

    public const UNCONNECTED_PONG_PACKET_ID = 0x1C;

    public const OFFLINE_MESSAGE_DATA_ID = [
        0x00, 0xFF, 0xFF, 0x00,
        0xFE, 0xFE, 0xFE, 0xFE,
        0xFD, 0xFD, 0xFD, 0xFD,
        0x12, 0x34, 0x56, 0x78,
    ];

    public int $pingId;
    public int $serverId;
    public string $gameId;
    public string $description;
    public BedrockProtocolVersion $protocol;
    public string $gameVersion;
    public int $currentPlayers;
    public int $maxPlayers;
    public string $name;
    public ?string $mode = null;

    public function getPacketId(): int
    {
        return self::PACKET_ID;
    }

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        if (self::UNCONNECTED_PONG_PACKET_ID !== $is->readByte()) {
            throw new PacketReadException(self::class, 'packet id is not UNCONNECTED_PONG');
        }

        $this->pingId = $is->readLong();
        $this->serverId = $is->readLong();

        if ($is->readBytes(16)->bytes() !== $this->getOfflineMessageDataBuffer()->bytes()) {
            throw new PacketReadException(self::class, 'magic bytes is difference');
        }

        // advertise length
        $is->readShort();

        $advertiseData = explode(';', $is->readFullData()->bytes());

        $this->gameId = $advertiseData[0];
        $this->description = $advertiseData[1];
        $this->protocol = BedrockProtocolVersion::from((int) $advertiseData[2]);
        $this->gameVersion = $advertiseData[3];
        $this->currentPlayers = (int) $advertiseData[4];
        $this->maxPlayers = (int) $advertiseData[5];
        $this->name = $advertiseData[7];
        $this->mode = $advertiseData[8] ?? null;
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeBytes($this->getOfflineMessageDataBuffer());
        $os->writeBytes((new ByteBuffer())->appendUint64(2));
    }

    public function getOfflineMessageDataBuffer(): ByteBuffer
    {
        $os = new ByteBufferOutputStream(new ByteBuffer());
        $os->writeBytes(self::OFFLINE_MESSAGE_DATA_ID);

        return $os->getBuffer();
    }
}
