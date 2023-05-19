<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use Loper\MinecraftQueryClient\Var\VarTypeReader;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class SocketInputStream implements InputStream
{
    public const CHUNK_SIZE = 8192;

    private Socket\Socket $socket;

    public function __construct(Socket\Socket $socket)
    {
        $this->socket = $socket;
    }

    public function readFullData(int $maxReadBytes = self::MAX_READ_BYTES): ByteBuffer
    {
        $data = '';
        $size = 0;

        while (null !== $d = $this->readRawBytes(self::CHUNK_SIZE)) {
            if ($size > $maxReadBytes) {
                throw DataSizeException::tooBigData();
            }

            $size += \strlen($d);
            $data .= $d;
        }

        return new ByteBuffer($data);
    }

    public function readVarInt(int $maxBytes = 5): int
    {
        return VarTypeReader::readVarInt($this, $maxBytes);
    }

    public function readByte(): int
    {
        return $this->readBytes(1)->readUint8();
    }

    public function readBytes(int $size): ByteBuffer
    {
        $buffer = $this->readRawBytes($size);

        if (null === $buffer) {
            throw SocketReadException::couldNotReadBytes();
        }

        return new ByteBuffer($buffer);
    }

    private function readRawBytes(int $size): ?string
    {
        /** @var \Socket $socket */
        $socket = $this->socket->getResource();

        $result = @\socket_recv($socket, $data, $size, \MSG_WAITALL);
        $code = \socket_last_error($socket);

        if (false === $result && !in_array($code, [\SOCKET_EINPROGRESS, \SOCKET_EAGAIN], true)) {
            throw SocketReadException::fromSocketError(\socket_strerror($code), $code);
        }

        return $data;
    }

    public function readInt(): int
    {
        return $this->readBytes(4)->readInt32();
    }

    public function readLong(): int
    {
        return $this->readBytes(8)->readInt64();
    }

    public function readShort(): int
    {
        return $this->readBytes(2)->readInt16();
    }

    public function readString(): ByteBuffer
    {
        $data = '';

        try {
            while (0x00 !== $byte = $this->readByte()) {
                $data .= \chr($byte);
            }
        } catch (SocketReadException) {
        }

        return new ByteBuffer($data);
    }

    public function readVarShort(): int
    {
        return VarTypeReader::readVarShort($this);
    }

    public function readUnsignedShort(): int
    {
        return $this->readBytes(2)->consumeUint16();
    }

    public function readUnsignedByte(): int
    {
        return $this->readBytes(1)->consumeUint8();
    }

    public function readLittleEndianUnsignedShort(): int
    {
        $result = \unpack("v", $this->readBytes(2)->consume(2));

        if (false === $result) {
            throw new \RuntimeException('Bad little endian unsigned short.');
        }

        return $result[1];
    }
}
