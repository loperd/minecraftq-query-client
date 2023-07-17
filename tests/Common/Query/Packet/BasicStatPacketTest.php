<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Common\Query\Packet;

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Common\Query\Packet\BasicStatPacket;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;
use Loper\MinecraftQueryClient\Tests\Helper\PacketFactory;

final class BasicStatPacketTest extends TestCase
{
    /**
     * @dataProvider packetDataProvider
     */
    public function test_successful_read_basicstat_packet(string $bytes, array $data): void
    {
        $buffer = new ByteBuffer($bytes);
        $is = new ByteBufferInputStream($buffer);
        $packet = new BasicStatPacket();
        $packet->read($is, JavaProtocolVersion::JAVA_1_12_2);

        self::assertEquals($data['motd'], $packet->motd);
        self::assertEquals($data['map'], $packet->map);
        self::assertEquals($data['numPlayers'], $packet->numPlayers);
        self::assertEquals($data['maxPlayers'], $packet->maxPlayers);
        self::assertEquals($data['port'], $packet->port);
        self::assertEquals($data['host'], $packet->host);
    }

    public static function packetDataProvider(): array
    {
        return [
            [base64_decode('AAAACwanOadsVUGnZadsUkFGVKc3IC0gVWtyYWluaWFuIE1pbmVjcmFmdCBTZXJ2ZXIhAFNNUAB3b3JsZAAwADEwAN1jMTI3LjAuMS4xAA==', true), [
                'motd' => '9lUAelRAFT7 - Ukrainian Minecraft Server!',
                'map' => 'world',
                'numPlayers' => 0,
                'maxPlayers' => 10,
                'port' => 25565,
                'host' => '127.0.1.1'
            ]],
        ];
    }


    /**
     * @dataProvider packetBadDataProvider
     */
    public function test_fake_read_basicstat_packet(array $data): void
    {
        $buffer = PacketFactory::createBasicStatBuffer($data['motd'], $data['smp'], $data['map'], $data['numPlayers'], $data['maxPlayers']);
        $is = new ByteBufferInputStream($buffer);

        $packet = new BasicStatPacket();
        $packet->read($is, JavaProtocolVersion::JAVA_1_12_2);

        self::assertEquals($data['motd'], $packet->motd);
        self::assertEquals($data['map'], $packet->map);
        self::assertEquals($data['numPlayers'], $packet->numPlayers);
        self::assertEquals($data['maxPlayers'], $packet->maxPlayers);
        self::assertEquals($data['host'], $packet->host);
    }

    public static function packetBadDataProvider(): array
    {
        return [
            [
                ['motd' => 'echo 123',
                  'smp' => 'smp',
                  'map' => 'world',
                  'numPlayers' => '0',
                  'maxPlayers' => '10',
                  'port' => '',
                  'host' => '127.0.1.1'
                ],
            ],
        ];
    }

    /**
     * @dataProvider packetWriteDataProvider
     */
    public function test_write_basicstat_packet(int $sessionId, int $token): void
    {
        $packet = new BasicStatPacket();
        $packet->sessionId = $sessionId;
        $packet->challengeToken = $token;

        $os = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($os, JavaProtocolVersion::JAVA_1_12_2);

        $outputBuffer = $os->getBuffer();
        $result = new ByteBufferInputStream($outputBuffer);
        self::assertEquals($sessionId, $result->readInt());
        self::assertEquals($token, $result->readInt());
    }

    public static function packetWriteDataProvider(): array
    {
        return [
            [123123123, 321321321],
        ];
    }
}
