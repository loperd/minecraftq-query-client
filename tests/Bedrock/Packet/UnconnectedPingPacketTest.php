<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Bedrock\Packet;

use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

final class UnconnectedPingPacketTest extends TestCase
{
    public function test_correct_read(string $bytes, array $data): void
    {
        $buffer = new ByteBuffer(base64_decode($bytes, true));
        $is = new ByteBufferInputStream($buffer);

        $packet = new UnconnectedPingPacket();
        $packet->read($is, BedrockProtocolVersion::BEDROCK_1_20_12);

        self::assertEquals($data['serverProtocol'], $packet->serverProtocol->value);
        self::assertEquals($data['onlinePlayers'], $packet->onlinePlayers);
        self::assertEquals($data['players'], $packet->players);
        self::assertEquals($data['rawData'], $packet->rawData);
        self::assertEquals($data['rawMotd'], $packet->rawMotd);
        self::assertEquals($data['motd'], $packet->motd);
        self::assertEquals($data['maxPlayers'], $packet->maxPlayers);
        self::assertEquals($data['serverSoftware'], $packet->serverSoftware);
    }

    public function test_correct_write(): void
    {
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
}
