<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Java\Packet;

use Loper\MinecraftQueryClient\Exception\InvalidPortException;
use Loper\MinecraftQueryClient\Java\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

final class HandshakePacketTest extends TestCase
{
    /**
     * @dataProvider packetDataProvider
     */
    public function test_successful_read_java_handshake_packet(string $bytes, array $data): void
    {
        $buffer = new ByteBuffer(base64_decode($bytes, true));
        $is = new ByteBufferInputStream($buffer);
        $packet = new HandshakePacket();
        $packet->read($is, JavaProtocolVersion::JAVA_1_12_2);

        self::assertEquals($data['serverProtocol'], $packet->serverProtocol->value);
        self::assertEquals($data['onlinePlayers'], $packet->onlinePlayers);
        self::assertEquals($data['players'], $packet->players);
        self::assertEquals($data['rawData'], $packet->rawData);
        self::assertEquals($data['rawMotd'], $packet->rawMotd);
        self::assertEquals($data['motd'], $packet->motd);
        self::assertEquals($data['maxPlayers'], $packet->maxPlayers);
        self::assertEquals($data['serverSoftware'], $packet->serverSoftware);
    }

    public static function packetDataProvider(): array
    {
        return [
            [
                'iAIAhQJ7ImRlc2NyaXB0aW9uIjp7ImV4dHJhIjpbeyJib2xkIjp0cnVlLCJjb2xvciI6ImJsdWUiLCJ0ZXh0IjoiVUEifSx7ImJvbGQiOnRydWUsImNvbG9yIjoieWVsbG93IiwidGV4dCI6IlJBRlQifSx7ImNvbG9yIjoiZ3JheSIsInRleHQiOiIgLSBVa3JhaW5pYW4gTWluZWNyYWZ0IFNlcnZlciEifV0sInRleHQiOiIifSwicGxheWVycyI6eyJtYXgiOjEwLCJvbmxpbmUiOjB9LCJ2ZXJzaW9uIjp7Im5hbWUiOiJQYXBlciAxLjE4LjIiLCJwcm90b2NvbCI6NzU4fX0=',
                [
                    "serverProtocol" => 758,
                    "onlinePlayers" => 0,
                    "maxPlayers" => 10,
                    "rawData" => "{\"description\":{\"extra\":[{\"bold\":true,\"color\":\"blue\",\"text\":\"UA\"},{\"bold\":true,\"color\":\"yellow\",\"text\":\"RAFT\"},{\"color\":\"gray\",\"text\":\" - Ukrainian Minecraft Server!\"}],\"text\":\"\"},\"players\":{\"max\":10,\"online\":0},\"version\":{\"name\":\"Paper 1.18.2\",\"protocol\":758}}",
                    "serverSoftware" => "Paper 1.18.2",
                    "rawMotd" => "{\"extra\":[{\"bold\":true,\"color\":\"blue\",\"text\":\"UA\"},{\"bold\":true,\"color\":\"yellow\",\"text\":\"RAFT\"},{\"color\":\"gray\",\"text\":\" - Ukrainian Minecraft Server!\"}],\"text\":\"\"}",
                    "motd" => "UARAFT - Ukrainian Minecraft Server!",
                    "players" => [],
                ]
            ],
        ];
    }

    /**
     * @dataProvider packetWriteDataProvider
     */
    public function test_write_java_handshake_packet(string $host, int $port, $state): void
    {
        $packet = new HandshakePacket();
        $packet->host = $host;
        $packet->port = $port;
        $packet->state = $state;
        $protocol = JavaProtocolVersion::JAVA_1_12_2;

        $os = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($os, $protocol);

        $outputBuffer = $os->getBuffer();
        $result = new ByteBufferInputStream($outputBuffer);
        self::assertEquals(HandshakePacket::PACKET_ID, $result->readByte());
        self::assertEquals($protocol->value, $result->readVarInt());

        // skip byte
        $result->readByte();

        self::assertEquals($host, $result->readBytes(\strlen($host)));
        self::assertEquals($port, $result->readShort());
        self::assertEquals($state, $result->readVarInt());
    }

    /**
     * @dataProvider packetFailedWriteDataProvider
     */
    public function test_java_invalid_handshake_packet(string $host, int $port, string $exceptionClass, ?string $exceptionMessage = null): void
    {
        $this->expectException($exceptionClass);

        if (null !== $exceptionMessage) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        $packet = new HandshakePacket();
        $packet->host = $host;
        $packet->port = $port;
        $protocol = JavaProtocolVersion::JAVA_1_12_2;

        $os = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($os, $protocol);
    }

    public static function packetWriteDataProvider(): array
    {
        return [
            ['51.233.21.21', 25565, 1],
        ];
    }

    public static function packetFailedWriteDataProvider(): array
    {
        return [
            ['51.233.21.21', -1, InvalidPortException::class, InvalidPortException::shouldBeUnsigned(-1)->getMessage()],
        ];
    }
}
