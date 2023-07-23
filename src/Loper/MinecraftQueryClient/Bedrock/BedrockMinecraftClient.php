<?php
declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Bedrock;

use JetBrains\PhpStorm\ArrayShape;
use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;
use Loper\MinecraftQueryClient\MinecraftClient;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;
use Loper\MinecraftQueryClient\Stream\SocketInputStream;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class BedrockMinecraftClient implements MinecraftClient
{
    private Socket\Socket $socket;
    private SocketInputStream $is;

    public function __construct(
        private readonly ServerAddress $serverAddress,
        private readonly ProtocolVersion $protocol,
        private readonly float $timeout = 1.5,
        private readonly Socket\Factory $factory = new Socket\Factory()
    ) {
        $this->socket = $this->createSocket($this->serverAddress, $this->timeout);
        $this->socket->setOption(SOL_SOCKET, SO_RCVTIMEO, $this->createSocketTimeout());

        $this->is = new SocketInputStream($this->socket);
    }

    /**
     * @return int[]
     */
    #[ArrayShape(['sec' => "int", 'usec' => "int"])]
    public function createSocketTimeout(): array
    {
        $seconds = (int)$this->timeout;
        $microseconds = (int)($this->timeout - $seconds) * 100000;

        return ['sec' => $seconds, 'usec' => $microseconds];
    }

    private function createSocket(ServerAddress $serverAddress, float $timeout): Socket\Socket
    {
        try {
            return $this->factory->createUdp4();
        } catch (Socket\Exception $ex) {
            throw new SocketConnectionException($serverAddress, $ex);
        }
    }

    public function sendPacket(Packet $packet): void
    {
        $buffer = new ByteBuffer();
        $buffer->appendInt8($packet->getPacketId());
        $buffer->appendUint64(time());

        $stream = new ByteBufferOutputStream($buffer);
        $packet->write($stream, $this->protocol);

        try {
            $this->socket->assertAlive();
        } catch (Socket\Exception) {
            throw new SocketConnectionException($this->serverAddress);
        }

        $this->socket->sendTo($stream->getBuffer()->bytes(), 0, $this->createRemoteAddress());

        $packet->read(new ByteBufferInputStream($this->is->readFullData()), $this->protocol);
    }

    public function createUnconnectedPingPacket(): UnconnectedPingPacket
    {
        return PacketFactory::createUnconnectedPingPacket($this->protocol);
    }

    public function close(): void
    {
        $this->socket->close();
    }

    private function createRemoteAddress(): string
    {
        return $this->serverAddress->format();
    }
}