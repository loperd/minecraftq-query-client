<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common\Query;

use JetBrains\PhpStorm\ArrayShape;
use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Common\Query\Packet\BasicStatPacket;
use Loper\MinecraftQueryClient\Common\Query\Packet\FullStatPacket;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Exception\PacketSendException;
use Loper\MinecraftQueryClient\MinecraftClient;
use Loper\MinecraftQueryClient\Packet;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class QueryMinecraftClient implements MinecraftClient
{
    public const MAGIC_BYTES = 0xFEFD;

    private Socket\Socket $socket;

    public function __construct(
        private readonly ServerAddress $serverAddress,
        private readonly float $timeout = 1.5,
        private readonly Socket\Factory $factory = new Socket\Factory()
    ) {
        $this->socket = $this->createSocket($this->serverAddress, $this->timeout);
        $this->socket->setOption(SOL_SOCKET, SO_RCVTIMEO, $this->createSocketTimeout());
        $this->socket->setBlocking(true);
    }

    private function createSocket(ServerAddress $serverAddress, float $timeout): Socket\Socket
    {
        try {
            return $this->factory->createClient(\sprintf('udp://%s', $serverAddress), $timeout);
        } catch (Socket\Exception $ex) {
            throw new SocketConnectionException($serverAddress, $ex);
        }
    }

    public function getChallengeToken(ProtocolVersion $protocol): ChallengeToken
    {
        $packet = PacketFactory::createHandshakePacket();

        try {
            $this->sendPacket($packet, $protocol);
        } catch (Socket\Exception|PacketReadException $ex) {
            throw new SocketConnectionException($this->serverAddress, $ex);
        }

        return $packet->challengeToken;
    }

    public function getFullStat(ChallengeToken $challengeToken, ProtocolVersion $protocol): FullStatPacket
    {
        $packet = PacketFactory::createFullStatPacket($challengeToken);

        $this->sendPacket($packet, $protocol);

        return $packet;
    }

    public function getBasicStat(ChallengeToken $challengeToken, ProtocolVersion $protocol): BasicStatPacket
    {
        $packet = PacketFactory::createBasicStatPacket($challengeToken);

        $this->sendPacket($packet, $protocol);

        return $packet;
    }

    public function close(): void
    {
        $this->socket->close();
    }

    public function sendPacket(Packet $packet, ProtocolVersion $protocol): void
    {
        $buffer = new ByteBuffer();
        $buffer->appendInt16(self::MAGIC_BYTES);
        $buffer->appendInt8($packet->getPacketId());
        $stream = new ByteBufferOutputStream($buffer);
        $packet->write($stream, $protocol);

        if ($buffer->size() !== $this->socket->send((string)$buffer, 0)) {
            throw new PacketSendException(\get_class($packet), $this->serverAddress);
        }

        $buffer = new ByteBuffer($this->socket->read(4096));

        // Check packet type by id
        if ($packet->getPacketId() !== $buffer->readInt8()) {
            throw new PacketReadException(\get_class($packet), 'Packet id is invalid.');
        }

        $packet->read(new ByteBufferInputStream($buffer), $protocol);
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
}
