<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Ping;

use JetBrains\PhpStorm\ArrayShape;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Exception\SocketConnectionException;
use Loper\MinecraftQueryClient\MinecraftClient;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Ping\Packet\HandshakePacket;
use Loper\MinecraftQueryClient\ServerStatsResponse;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Stream\SocketInputStream;
use Loper\MinecraftQueryClient\Stream\SocketOutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Loper\MinecraftQueryClient\Structure\VersionProtocolMap;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class TCPMinecraftClient implements MinecraftClient
{
    private Socket\Socket $socket;
    private SocketInputStream $is;
    private SocketOutputStream $os;

    public function __construct(
        private readonly ServerAddress $serverAddress,
        private readonly float         $timeout = 1.5,
        private readonly ProtocolVersion $protocol = ProtocolVersion::VER_1_7_2
    ) {
        $this->socket = $this->createSocket($this->serverAddress, $this->timeout);
        $this->socket->setOption(SOL_SOCKET, SO_RCVTIMEO, $this->createSocketTimeout());

        $this->os = new SocketOutputStream($this->socket);
        $this->is = new SocketInputStream($this->socket);
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

    public function getStats(): ServerStatsResponse
    {
        $packet = PacketFactory::createHandshakePacket(
            $this->serverAddress,
            $this->protocol
        );

        $this->sendPacket($packet);

        return $this->createServerStatsResponse($packet);
    }

    private function sendPacket(Packet $packet): void
    {
        $stream = new ByteBufferOutputStream(new ByteBuffer());
        $packet->write($stream, $this->protocol);

        $this->os->writeByte($stream->getBuffer()->size());
        $this->os->writeBytes($stream->getBuffer());
        $this->os->writeByte(0x01);
        $this->os->writeByte(0x00);

        $packet->read($this->is, $this->protocol);
    }

    public function __destruct()
    {
        $this->socket->close();
    }

    public function close(): void
    {
        $this->socket->close();
    }

    private function createServerStatsResponse(HandshakePacket $packet): ServerStatsResponse
    {
        $version = VersionProtocolMap::getByProtocol($packet->serverProtocol);

        $response = new ServerStatsResponse();
        $response->version = $version;
        $response->protocol = $packet->serverProtocol;
        $response->serverSoftware = $packet->serverSoftware;
        $response->maxPlayers = $packet->maxPlayers;
        $response->numPlayers = $packet->onlinePlayers;
        $response->motd = $packet->motd;
        $response->players = $packet->players;

        return $response;
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
