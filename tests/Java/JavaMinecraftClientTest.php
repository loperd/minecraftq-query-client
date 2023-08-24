<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Java;

use DG\BypassFinals;
use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Address\ServerAddressType;
use Loper\MinecraftQueryClient\Java\JavaMinecraftClient;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;
use Loper\MinecraftQueryClient\Tests\TestPacket;
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

        $serverAddress = new ServerAddress(ServerAddressType::Dedicated, '1.1.1.1', '1.1.1.1');
        return new JavaMinecraftClient($serverAddress, 1.5, $mockSocketFactory);
    }

    private function createSocket(): Socket&MockObject
    {
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(SOCK_STREAM);

        return $socket;
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

        $javaClient->sendPacket($packet, JavaProtocolVersion::JAVA_1_20_1);

        $this->assertTrue($packet->readed);
    }

    public function test_error_java_socket_exception(): void
    {
        $this->expectException(SocketConnectionException::class);
        $socket = $this->createSocket();
        $socket->method('assertAlive')->withAnyParameters()->willThrowException(Exception::createFromCode(1));

        $javaClient = $this->getJavaClient($socket);
        $packet = new TestPacket();

        $javaClient->sendPacket($packet, JavaProtocolVersion::JAVA_1_20_1);
    }
}
