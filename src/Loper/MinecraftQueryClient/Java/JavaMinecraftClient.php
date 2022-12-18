<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use JetBrains\PhpStorm\ArrayShape;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Java\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\MinecraftClient;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;
use Loper\MinecraftQueryClient\Stream\SocketInputStream;
use Loper\MinecraftQueryClient\Stream\SocketOutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class JavaMinecraftClient implements MinecraftClient
{
    private Socket\Socket $socket;
    private SocketInputStream $is;
    private SocketOutputStream $os;

    public function __construct(
        private readonly ServerAddress $serverAddress,
        private readonly ProtocolVersion $protocol,
        private readonly float         $timeout = 1.5,
    ) {
        $this->socket = $this->createSocket($this->serverAddress, $this->timeout);
        $this->socket->setOption(SOL_SOCKET, SO_RCVTIMEO, $this->createSocketTimeout());

        $this->os = new SocketOutputStream($this->socket);
        $this->is = new SocketInputStream($this->socket);
    }

    public function createHandshakePacket(): HandshakePacket
    {
        return PacketFactory::createHandshakePacket(
            $this->serverAddress,
            $this->protocol);
    }

    private function createSocket(ServerAddress $serverAddress, float $timeout): Socket\Socket
    {
        $factory = new Socket\Factory();

        try {
            $address = \sprintf('tcp://%s', $serverAddress);

            return $factory->createClient($address, $timeout);
        } catch (Socket\Exception $ex) {
            throw new SocketConnectionException($serverAddress, $ex);
        }
    }

    public function sendPacket(Packet $packet): void
    {
        $stream = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($stream, $this->protocol);

        $this->os->writeByte($stream->getBuffer()->size());
        $this->os->writeBytes($stream->getBuffer());
        $this->os->writeByte(0x01);
        $this->os->writeByte(0x00);

        $packet->read($this->is, $this->protocol);
    }

    public function close(): void
    {
        $this->socket->close();
    }

    /**
     * @return int[]
     */
    #[ArrayShape(['sec' => "int", 'usec' => "int"])]
    public function createSocketTimeout(): array
    {
        $seconds = (int) $this->timeout;
        $microseconds = (int) ($this->timeout - $seconds) * 100000;

        return ['sec' => $seconds, 'usec' => $microseconds];
    }
}
