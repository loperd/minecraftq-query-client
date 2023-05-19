<?php

declare(strict_types=1);

namespace Loper\Tests\Java;

use DG\BypassFinals;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Java\JavaMinecraftClient;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Loper\Tests\TestPacket;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Socket\Raw\Exception;
use Socket\Raw\Factory;
use Socket\Raw\Socket;

class JavaMinecraftClientTest extends TestCase
{
    /**
     * @return \Loper\MinecraftQueryClient\Java\JavaMinecraftClient
     */
    public function getJavaClient(?Socket $socket = null): JavaMinecraftClient
    {
        $socket ??= $this->createSocket();

        $mockSocketFactory = $this->createMock(Factory::class);
        $mockSocketFactory->method('createClient')->withAnyParameters()->willReturn($socket);

        $serverAddress = new ServerAddress('1.1.1.1', '1.1.1.1');
        return new JavaMinecraftClient($serverAddress, ProtocolVersion::JAVA_1_12_2, 1.5, $mockSocketFactory);
    }

    private function createSocket(): Socket&MockObject
    {
        return $this->createMock(Socket::class);
    }

    protected function setUp(): void
    {
        BypassFinals::enable();
    }

    public function test_send_java_packet(): void
    {
        $socket = $this->createSocket();
        $socket->expects($this->atLeastOnce())->method('write')->withAnyParameters();

        $javaClient = $this->getJavaClient($socket);
        $packet = new TestPacket();

        $javaClient->sendPacket($packet);

        $this->assertTrue($packet->testCase);
    }

    public function test_error_java_socket_exception(): void
    {
        $this->expectException(SocketConnectionException::class);
        $socket = $this->createSocket();
        $socket->method('assertAlive')->withAnyParameters()->willThrowException(Exception::createFromCode(1));

        $javaClient = $this->getJavaClient($socket);
        $packet = new TestPacket();

        $javaClient->sendPacket($packet);
    }
}
