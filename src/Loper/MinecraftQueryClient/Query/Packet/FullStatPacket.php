<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Query\Packet;

use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

final class FullStatPacket implements Packet
{
    public const PACKET_ID = 0x00;

    public int $sessionId;
    public int $challengeToken;

    // Response data
    /** @var string[] */
    public array $plugins = [];

    public string $version;
    public string $map;
    public int $numPlayers;
    public int $maxPlayers;
    public int $port;
    public string $host;

    /** @var string[] */
    public array $players = [];

    public function getPacketId(): int
    {
        return self::PACKET_ID;
    }

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        // Remove zero bytes and \rsplitnum string
        $is->readBytes(16);

        $buffer = $is->readFullData();

        if (false === $pos = \strpos($buffer->bytes(), 'player_')) {
            throw new PacketReadException(self::class, 'Packet is not complete.');
        }

        $data = \explode("\x0", $buffer->consume($pos));

        $plugins = $this->getPlugins($data[9]);

        $this->version = $data[7];
        $this->plugins = $plugins;
        $this->map = $data[11];
        $this->numPlayers = (int) $data[13];
        $this->maxPlayers = (int) $data[15];
        $this->port = (int) $data[17];
        $this->host = $data[19];

        // consume "\x0\x1player_" word with two bytes
        $buffer->consume(9);

        // consume all without 2 bytes at the end
        $players = $buffer->consume($buffer->size() - 2);
        $this->players = \explode("\x0", $players);
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeInt($this->sessionId);
        $os->writeInt($this->challengeToken);
        $os->writeInt(0x00);
    }

    /**
     * @return string[]
     */
    private function getPlugins(string $input): array
    {
        if ('' === $input) {
            return [];
        }

        $parts = \explode(': ', $input);

        if (2 !== \count($parts)) {
            return [];
        }

        return \explode('; ', $parts[1]);
    }
}
