<?php

namespace Loper\Tests\Common\Query\Packet;

use Loper\MinecraftQueryClient\Common\Query\Packet\FullStatPacket;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

final class FullStatPacketTest extends TestCase
{
    /**
     * @dataProvider packetDataProvider
     */
    public function test_successful_read_fullstat_packet(string $bytes, array $data): void
    {
        $buffer = new ByteBuffer($bytes);
        $is = new ByteBufferInputStream($buffer);
        $packet = new FullStatPacket();
        $packet->read($is, ProtocolVersion::JAVA_1_12_2);

        self::assertEquals($data['serverProtocol'], $packet->serverProtocol->value);
        self::assertEquals($data['version'], $packet->version->value);
        self::assertEquals($data['plugins'], $packet->plugins);
        self::assertEquals($data['players'], $packet->players);
        self::assertEquals($data['map'], $packet->map);
        self::assertEquals($data['numPlayers'], $packet->numPlayers);
        self::assertEquals($data['maxPlayers'], $packet->maxPlayers);
        self::assertEquals($data['port'], $packet->port);
        self::assertEquals($data['host'], $packet->host);
    }

    public function packetDataProvider(): array
    {
        return [
            [
                base64_decode('AAAACAhzcGxpdG51bQCAAGhvc3RuYW1lAKc5p2xVQadlp2xSQUZUpzcgLSBVa3JhaW5pYW4gTWluZWNyYWZ0IFNlcnZlciEAZ2FtZXR5cGUAU01QAGdhbWVfaWQATUlORUNSQUZUAHZlcnNpb24AMS4xOC4yAHBsdWdpbnMAUGFwZXIgb24gMS4xOC4yLVIwLjEtU05BUFNIT1Q6IEJldHRlclNsZWVwaW5nNCA0LjAuMTsgTHVja1Blcm1zIDUuNC4xNjsgVmF1bHQgMS43LjMtYjEzMTsgUHJvdG9jb2xMaWIgNC44LjA7IFNraW5zUmVzdG9yZXIgMTQuMi4zOyBXb3JsZEVkaXQgNy4yLjEwKzE3NDJmOTg7IENvcmVQcm90ZWN0IDIxLjI7IENNSUxpYiAxLjIuMS4wOyBDTUkgOS4xLjMuMzsgUGx1Z01hblggMi4zLjAAbWFwAHdvcmxkAG51bXBsYXllcnMAMABtYXhwbGF5ZXJzADEwAGhvc3Rwb3J0ADI1NTY1AGhvc3RpcAAxMjcuMC4xLjEAAAFwbGF5ZXJfAAAA'),
                [
                    "version" => "1.18.2",
                    "map" => "world",
                    "numPlayers" => 0,
                    "maxPlayers" => 10,
                    "port" => 25565,
                    "host" => "127.0.1.1",
                    "players" => [],
                    "serverProtocol" => 758,
                    "plugins" => [
                        "BetterSleeping4 4.0.1",
                        "LuckPerms 5.4.16",
                        "Vault 1.7.3-b131",
                        "ProtocolLib 4.8.0",
                        "SkinsRestorer 14.2.3",
                        "WorldEdit 7.2.10+1742f98",
                        "CoreProtect 21.2",
                        "CMILib 1.2.1.0",
                        "CMI 9.1.3.3",
                        "PlugManX 2.3.0"
                    ],
                ]
            ],
        ];
    }

    /**
     * @dataProvider packetWriteDataProvider
     */
    public function test_write_fullstat_packet(int $sessionId, int $token): void
    {
        $packet = new FullStatPacket();
        $packet->sessionId = $sessionId;
        $packet->challengeToken = $token;

        $os = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($os, ProtocolVersion::JAVA_1_12_2);

        $outputBuffer = $os->getBuffer();
        $result = new ByteBufferInputStream($outputBuffer);
        self::assertEquals($sessionId, $result->readInt());
        self::assertEquals($token, $result->readInt());
        self::assertEquals(0x00, $result->readInt());
    }

    public function packetWriteDataProvider(): array
    {
        return [
            [123123123, 321321321],
        ];
    }
}
