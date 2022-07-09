<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Query\Packet;

use Composer\Semver\Semver;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Service\VarUnsafeFilter;
use Loper\MinecraftQueryClient\Service\VersionParser;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ServerVersion;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Loper\MinecraftQueryClient\Structure\VersionProtocolMap;

final class FullStatPacket implements Packet
{
    public const PACKET_ID = 0x00;

    // Request Data
    public int $sessionId;
    public int $challengeToken;

    // Response Data
    public ServerVersion $version;
    public string $map;
    public int $numPlayers;
    public int $maxPlayers;
    public int $port;
    public string $host;

    /** @var string[] */
    public array $players = [];

    /** @var string[] */
    public array $plugins = [];

    public ProtocolVersion $serverProtocol;

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

        $version = VersionParser::parse(VarUnsafeFilter::filter($data[7]));
        $this->serverProtocol = VersionProtocolMap::getByVersion($version);
        $this->version = $version;
        $this->plugins = $plugins;
        $this->map = VarUnsafeFilter::filter($data[11]);
        $this->numPlayers = (int) VarUnsafeFilter::filter($data[13]);
        $this->maxPlayers = (int) VarUnsafeFilter::filter($data[15]);
        $this->port = (int) VarUnsafeFilter::filter($data[17]);
        $this->host = VarUnsafeFilter::filter($data[19]);

        // consume "\x0\x1player_" word with two bytes
        $buffer->consume(9);

        // consume all without 2 bytes at the end
        $players = VarUnsafeFilter::filter(
            $buffer->consume($buffer->size() - 2)
        );
        $this->players = \explode("&#0;", $players);
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

        $parts = \explode(': ', VarUnsafeFilter::filter($input));

        if (2 !== \count($parts)) {
            return [];
        }

        return \explode('; ', $parts[1]);
    }
}
