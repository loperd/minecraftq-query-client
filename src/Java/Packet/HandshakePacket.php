<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Packet;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Exception\InvalidPortException;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\InputStream;
use Loper\MinecraftQueryClient\Stream\OutputStream;
use Loper\MinecraftQueryClient\Var\VarMotdFilter;
use Loper\MinecraftQueryClient\Var\VarUnsafeFilter;

final class HandshakePacket implements Packet
{
    public const EXPECTED_MIN_LENGTH = 10;
    public const MAX_JSON_SIZE = 1024 * 500;
    public const PACKET_ID = 0x00;
    public const STATUS = 1;

    public JavaProtocolVersion $protocol;

    public JavaProtocolVersion $serverProtocol;
    public string $host;
    public int $port;
    public int $state;
    public int $onlinePlayers;
    public int $maxPlayers;
    public string $rawData;
    public string $serverSoftware;

    public string $rawMotd;
    public string $motd;
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
            $buffer = $is->readBytes($jsonSize);
            $this->rawData = $buffer->bytes();

            $order = \mb_detect_order();
            $encoding = (string) \mb_detect_encoding($this->rawData, \is_array($order) ? $order : null, true);
            if ('UTF-8' !== $encoding) {
                $this->rawData = \mb_convert_encoding($this->rawData, $encoding, 'UTF-8');
            }

            /** @var array{
             *     version: array{
             *          protocol: int,
             *          name: string
             *     },
             *     players: array{
             *          max: int|string,
             *          online: int|string,
             *          sample: null|array{array{name: string, id: string}},
             *     },
             *     description: array{
             *          extra: array{array{bold: bool, color: string, text: string}},
             *          text: string,
             *     },
             *     favicon: string,
             *     bar: string
             * } $data
             */
            $data = \json_decode($this->rawData, true, flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException) {
            throw new PacketReadException(self::class, "Invalid packet json data");
        }

        $this->serverProtocol = JavaProtocolVersion::tryFrom($data['version']['protocol']) ?? JavaProtocolVersion::Unknown;
        $this->serverSoftware = VarUnsafeFilter::filter($data['version']['name']);
        $this->onlinePlayers = (int) VarUnsafeFilter::filter((string) $data['players']['online']);
        $this->maxPlayers = (int) VarUnsafeFilter::filter((string) $data['players']['max']);

        if (isset($data['players']['sample']) && \is_array($data['players']['sample'])) {
            $this->players = $this->getPlayers($data['players']['sample']);
        }

        $rawMotd = (string)\json_encode($data['description'], flags: JSON_THROW_ON_ERROR | JSON_ERROR_UTF8);

        $this->rawMotd = VarUnsafeFilter::filter($rawMotd);
        $this->motd = $this->formatMotd($data['description']);
    }

    public function write(OutputStream $os, ProtocolVersion $protocol): void
    {
        if ($this->port < 0) {
            throw InvalidPortException::shouldBeUnsigned($this->port);
        }

        $os->writeByte(0x00);
        $os->writeVarInt($protocol->getProtocolValue());
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

            $result[] = VarUnsafeFilter::filter($player['name']);
        }

        return $result;
    }

    /**
     * @param array{
     *     extra: array<array-key, array{
     *         bold: bool,
     *         color: string,
     *         text: string
     *      }>,
     *     text?: string,
     *     translate?: string
     * } $description
     */
    private function formatMotd(array $description): string
    {
        $process = static fn (string $input) => VarMotdFilter::filter($input);

        $text = $process($description['text'] ?? $description['translate'] ?? '');
        foreach ($description['extra'] ?? [] as $item) {
            if (!is_array($item)) {
                $text .= $process($item);
                continue;
            }

            $text .= $process($item['text']);
            $text .= (!isset($item['extra']) ? '' : $this->formatMotd($item));
        }

        $filteredText = preg_replace(
            pattern: '/\s{2,}/',
            replacement: ' ',
            subject: $text
        );

        return trim(string: $filteredText, characters: ' ');
    }
}
