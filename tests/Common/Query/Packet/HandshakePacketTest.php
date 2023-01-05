<?php

declare(strict_types=1);

namespace Loper\Tests\Common\Query\Packet;

use Loper\MinecraftQueryClient\Common\Query\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;
use Loper\Tests\Helper\PacketFactory;

final class HandshakePacketTest extends TestCase
{
    /**
     * @dataProvider packetDataProvider
     */
    public function test_successful_read_handshake_packet(string $bytes, array $data): void
    {
        $buffer = new ByteBuffer($bytes);
        $is = new ByteBufferInputStream($buffer);
        $packet = new HandshakePacket();
        $packet->read($is, ProtocolVersion::JAVA_1_12_2);

        self::assertEquals($data['sessionId'], $packet->sessionId);
        self::assertEquals($data['challengeToken'], $packet->challengeToken->token);
        self::assertEquals($data['sessionId'], $packet->challengeToken->sessionId);
    }

    public function packetDataProvider(): array
    {
        return [
            [base64_decode('CQAABwwxMDA1MjY5MwA='), [
                'sessionId' => 150994951,
                'challengeToken' => 10052693,
            ]],
        ];
    }

    /**
     * @dataProvider packetBadDataProvider
     */
    public function test_fake_read_handshake_packet(int $sessionId, string $token): void
    {
        $buffer = PacketFactory::createHandshakeBuffer($sessionId, $token);
        $is = new ByteBufferInputStream($buffer);

        $packet = new HandshakePacket();
        $packet->read($is, ProtocolVersion::JAVA_1_12_2);

        self::assertEquals($sessionId, $packet->sessionId);
        self::assertEquals((int)$token, $packet->challengeToken->token);
        self::assertEquals($sessionId, $packet->challengeToken->sessionId);
    }

    public function packetBadDataProvider(): array
    {
        return [
            [123123123, '321321321'],
        ];
    }

    /**
     * @dataProvider packetWriteDataProvider
     */
    public function test_write_handshake_packet(int $sessionId): void
    {
        $buffer = new ByteBuffer(base64_decode('CQAABwwxMDA1MjY5MwA='));
        $is = new ByteBufferInputStream($buffer);
        $packet = new HandshakePacket();
        $packet->read($is, ProtocolVersion::JAVA_1_12_2);

        $os = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($os, ProtocolVersion::JAVA_1_12_2);

        $outputBuffer = $os->getBuffer();
        $result = new ByteBufferInputStream($outputBuffer);
        self::assertEquals($sessionId, $result->readInt());
    }

    public function packetWriteDataProvider(): array
    {
        return [
            [150994951],
        ];
    }
}


