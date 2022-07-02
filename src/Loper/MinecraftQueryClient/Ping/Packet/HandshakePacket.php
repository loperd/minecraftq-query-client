<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Ping\Packet;

use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Ramsey\Uuid\Uuid;

final class HandshakePacket implements Packet
{
    public const EXPECTED_MIN_LENGTH = 10;
    public const MAX_JSON_SIZE = 1024 * 500;
    public const PACKET_ID = 0x00;
    public const STATUS = 1;

    public ProtocolVersion $protocol;

    public ProtocolVersion $serverProtocol;
    public string $host;
    public int $port;
    public int $state;
    public int $onlinePlayers;
    public int $maxPlayers;
    public string $description;
    public string $serverSoftware;
    public string $rawData;

    /** @var string[] */
    public array $players = [];

    public function read(InputStream $is, ProtocolVersion $protocol): void
    {
        $size = $is->readVarInt();
        $packetId = $is->readVarInt();

        if (self::EXPECTED_MIN_LENGTH > $size || self::PACKET_ID !== $packetId) {
            throw new PacketReadException(self::class, "Bad packet size or packet id");
        }

        // Read size of JSON
        $jsonSize = $is->readVarInt();

        if (self::MAX_JSON_SIZE < $jsonSize) {
            throw new PacketReadException(self::class, "Too big packet json data.");
        }

        try {
            $this->rawData = $is->readFullData(self::MAX_JSON_SIZE)->bytes();
            /** @var array{
             *     version: array{
             *          protocol: int,
             *          name: string
             *     },
             *     players: array{
             *          max: int,
             *          online: int,
             *          sample: null|array{array{name: string, id: string}},
             *     },
             *     description: array{
             *          extra: array{array{color: string, text: string}},
             *          text: string,
             *     },
             *     favicon: string,
             *     bar: string
             * } $data
             */
            $data = \json_decode($this->rawData, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException|\RuntimeException) {
            throw new PacketReadException(self::class, "Invalid packet json data");
        }

        $this->serverProtocol = ProtocolVersion::from($data['version']['protocol']);
        $this->serverSoftware = $data['version']['name'];
        $this->onlinePlayers = $data['players']['online'];
        $this->maxPlayers = $data['players']['max'];

        if (isset($data['players']['sample'])) {
            $this->players = $this->getPlayers($data['players']['sample']);
        }

        $this->description = (string) \json_encode($data['description']);
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        $os->writeByte(0x00);
        $os->writeVarInt($protocol->value);
        $os->writeVarString($this->host);
        $os->writeShort($this->port);
        $os->writeVarInt($this->state);
    }

    public function getPacketId(): int
    {
        return self::PACKET_ID;
    }

    /**
     * @param array<array-key, array<string, string>> $players
     *
     * @return string[]
     */
    private function getPlayers(array $players): array
    {
        $result = [];

        foreach ($players as $player) {
            if ('00000000-0000-0000-0000-000000000000' === $player['id']) {
                continue;
            }

            $result[] = $player['name'];
        }

        return $result;
    }
}
