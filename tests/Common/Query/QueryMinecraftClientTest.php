<?php

namespace Loper\Tests\Common\Query;

use DG\BypassFinals;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Common\Query\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\Common\Query\QueryMinecraftClient;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Exception\PacketSendException;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Loper\Tests\TestPacket;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Socket\Raw\Socket;

class QueryMinecraftClientTest extends TestCase
{
    /**
     * @return \Loper\MinecraftQueryClient\Common\Query\QueryMinecraftClient
     */
    public function getQueryClient(?Socket $socket = null): QueryMinecraftClient
    {
        $socket ??= $this->createSocket();

        return $this->createQueryClient($socket);
    }

    public function createQueryClient(Socket $socket): QueryMinecraftClient
    {
        $reflection = new \ReflectionClass(QueryMinecraftClient::class);
        $queryClient = $reflection->newInstanceWithoutConstructor();
        $socketProperty = $reflection->getProperty('socket');
        $socketProperty->setValue($queryClient, $socket);
        $protocolProperty = $reflection->getProperty('protocol');
        $protocolProperty->setValue($queryClient, ProtocolVersion::JAVA_1_12);
        $serverAddress = new ServerAddress('1.1.1.1', '1.1.1.1');
        $serverAddressProperty = $reflection->getProperty('serverAddress');
        $serverAddressProperty->setValue($queryClient, $serverAddress);

        return $queryClient;
    }

    /**
     * @param int $sendResult - size of written bytes
     */
    private function createSocket(string $socketData = 'CQAABwIxMjMyODkzNwA=', int $sendResult = 7): Socket&MockObject
    {
        $mockSocket = $this->createMock(Socket::class);
        $mockSocket->method('read')->withAnyParameters()->willReturn(base64_decode($socketData));
        $mockSocket->method('send')->withAnyParameters()->willReturn($sendResult);

        return $mockSocket;
    }

    protected function setUp(): void
    {
        BypassFinals::enable();
    }

    public function test_query_send_packet(): void
    {

        $socket = $this->createSocket(socketData: 'AA==');
        $socket->expects($this->atLeastOnce())->method('read')->withAnyParameters();

        $queryClient = $this->getQueryClient($socket);
        $packet = new TestPacket();

        $queryClient->sendPacket($packet);

        $this->assertTrue($packet->testCase);
    }

    public function test_query_send_packet_exception(): void
    {
        $this->expectException(PacketSendException::class);

        $socket = $this->createSocket(sendResult: 1);

        $queryClient = $this->getQueryClient($socket);
        $packet = new HandshakePacket();
        $packet->sessionId = 1794;

        $queryClient->sendPacket($packet);
    }

    public function test_query_read_packet_exception(): void
    {
        $this->expectException(PacketReadException::class);

        $buffer = new ByteBuffer();
        $buffer->appendInt8(1500);
        $encodeBuffer = base64_encode($buffer);

        $socket = $this->createSocket(socketData: $encodeBuffer);

        $queryClient = $this->getQueryClient($socket);
        $packet = new HandshakePacket();
        $packet->sessionId = 1794;

        $queryClient->sendPacket($packet);
    }
}


