<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Bedrock\Packet;

use DG\BypassFinals;
use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Address\ServerAddressType;
use Loper\MinecraftQueryClient\Bedrock\BedrockMinecraftClient;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;
use Loper\MinecraftQueryClient\Tests\TestPacket;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Socket\Raw\Exception;
use Socket\Raw\Factory;
use Socket\Raw\Socket;

class BedrockMinecraftClientTest extends TestCase
{
    public function createClient(MockObject $mockSocketFactory, float $timeout = 1.5): BedrockMinecraftClient {
        $serverAddress = new ServerAddress(
            ServerAddressType::Dedicated,
            '127.0.0.1',
            '127.0.0.1',
            19132
        );

        /**
         * @var Factory $mockSocketFactory
         */
        return new BedrockMinecraftClient($serverAddress, $timeout, $mockSocketFactory);
    }

    protected function setUp(): void
    {
        BypassFinals::enable();
    }

    public function test_success_send_packet(): void
    {
        $protocol = BedrockProtocolVersion::BEDROCK_1_20_12;
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->atLeastOnce())->method('sendTo');

        $mockSocketFactory = $this->createMock(Factory::class);
        $mockSocketFactory->method('createUdp4')
            ->withAnyParameters()
            ->willReturn($socket);

        $packet = new TestPacket();
        $client = $this->createClient($mockSocketFactory);
        $client->sendPacket($packet, $protocol);

        self::assertTrue($packet->readed);
    }

    public function test_doesnt_alive_socket(): void
    {
        $this->expectException(SocketConnectionException::class);
        $this->expectExceptionMessage('Could not connect to the server: "127.0.0.1:19132"');

        $socket = $this->createMock(Socket::class);
        $socket
            ->expects($this->once())
            ->method('assertAlive')
            ->withAnyParameters()
            ->willThrowException(Exception::createFromCode(SOCKET_EHOSTDOWN));

        $socketFactory = $this->createMock(Factory::class);
        $socketFactory->method('createUdp4')
            ->withAnyParameters()
            ->willReturn($socket);

        $this->createClient($socketFactory)->sendPacket(
            new TestPacket(),
            BedrockProtocolVersion::BEDROCK_1_20_12
        );
    }
}
