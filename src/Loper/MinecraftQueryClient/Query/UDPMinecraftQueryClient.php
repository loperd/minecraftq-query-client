<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Query;

use JetBrains\PhpStorm\ArrayShape;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\HandshakeFailedException;
use Loper\MinecraftQueryClient\MinecraftQueryClient;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Query\Packet\BasicStatPacket;
use Loper\MinecraftQueryClient\Query\Packet\FullStatPacket;
use Loper\MinecraftQueryClient\ServerStatsResponse;
use Loper\MinecraftQueryClient\SocketConnectionException;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class UDPMinecraftQueryClient implements MinecraftQueryClient
{
    public const MAGIC_BYTES = 0xFEFD;

    private Socket\Socket $socket;

    public function __construct(
        private readonly ServerAddress $serverAddress,
        private readonly float $timeout = 1.5,
        private readonly ProtocolVersion $protocol = ProtocolVersion::VER_1_7_2,
    ) {
        $this->socket = $this->createSocket($this->serverAddress, $this->timeout);
        $this->socket->setOption(SOL_SOCKET, SO_RCVTIMEO, $this->createSocketTimeout());
        $this->socket->setBlocking(true);

        try {
            $this->getChallengeToken();
        } catch (Socket\Exception|HandshakeFailedException $ex) {
            throw new SocketConnectionException($this->serverAddress, $ex);
        }
    }

    private function createSocket(ServerAddress $serverAddress, float $timeout): Socket\Socket
    {
        $factory = new Socket\Factory();

        try {
            return $factory->createClient(\sprintf('udp://%s', $serverAddress), $timeout);
        } catch (Socket\Exception $ex) {
            throw new SocketConnectionException($serverAddress, $ex);
        }
    }

    public function getFullStat(ChallengeToken $challengeToken): FullStatPacket
    {
        $packet = PacketFactory::createFullStatPacket($challengeToken);

        $this->sendPacket($packet);

        return $packet;
    }

    public function getBasicStat(ChallengeToken $challengeToken): BasicStatPacket
    {
        $packet = PacketFactory::createBasicStatPacket($challengeToken);

        $this->sendPacket($packet);

        return $packet;
    }

    public function getStats(): ServerStatsResponse
    {
        $challengeToken = $this->getChallengeToken();

        $basicStatPacket = $this->getBasicStat($challengeToken);
        $fullStatPacket = $this->getFullStat($challengeToken);

        return $this->createResponse($fullStatPacket, $basicStatPacket);
    }

    public function __destruct()
    {
        $this->socket->close();
    }

    public function close(): void
    {
        $this->socket->close();
    }

    private function sendPacket(Packet $packet): void
    {
        $buffer = new ByteBuffer();
        $buffer->appendInt16(self::MAGIC_BYTES);
        $buffer->appendInt8($packet->getPacketId());

        $stream = new ByteBufferOutputStream($buffer);
        $packet->write($stream, $this->protocol);

        if ($buffer->size() !== $this->socket->send((string) $buffer, 0)) {
            throw new Socket\Exception('Can not write packet.');
        }

        $buffer = new ByteBuffer($this->socket->read(4096));

        // Check packet type by id
        if ($packet->getPacketId() !== $buffer->readInt8()) {
            throw new HandshakeFailedException('Invalid packet type.');
        }

        $packet->read(new ByteBufferInputStream($buffer), $this->protocol);
    }

    private function getChallengeToken(): ChallengeToken
    {
        $packet = PacketFactory::createHandshakePacket();

        $this->sendPacket($packet);

        return $packet->challengeToken;
    }

    private function createResponse(FullStatPacket $fullStatPacket, BasicStatPacket $basicStatPacket): ServerStatsResponse
    {
        $response = new ServerStatsResponse();
        $response->version = $fullStatPacket->version;
        $response->plugins = $fullStatPacket->plugins;
        $response->map = $fullStatPacket->map;
        $response->numPlayers = $fullStatPacket->numPlayers;
        $response->maxPlayers = $fullStatPacket->maxPlayers;
        $response->port = $fullStatPacket->port;
        $response->host = $fullStatPacket->host;
        $response->players = $fullStatPacket->players;
        $response->motd = $basicStatPacket->motd;

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
