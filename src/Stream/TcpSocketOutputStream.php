<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use Loper\MinecraftQueryClient\Var\VarTypeFactory;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class TcpSocketOutputStream implements OutputStream
{
    private Socket\Socket $socket;

    public function __construct(Socket\Socket $socket)
    {
        if ($socket->getType() !== SOCK_STREAM) {
            throw new \InvalidArgumentException('Socket should be of SOCK_STREAM type.');
        }

        $this->socket = $socket;
    }

    /**
     * @param int[]|ByteBuffer|string $bytes
     */
    public function writeBytes(array|ByteBuffer|string $bytes): void
    {
        if (is_array($bytes)) {
            $bytes = ArrayByteBufferConverter::convert($bytes);
        }

        $buffer = (string) $bytes;

        if ('' === $buffer) {
            throw DataSizeException::emptyBuffer();
        }

        if (0 === $this->socket->write($buffer)) {
            throw SocketWriteException::unableToWrite();
        }
    }

    public function writeVarInt(int $data): void
    {
        $this->writeBytes(VarTypeFactory::createVarInt($data));
    }

    public function writeByte(int $byte): void
    {
        $this->writeBytes((new ByteBuffer())->appendInt8($byte));
    }

    public function writeShort(int $short): void
    {
        $this->writeBytes((new ByteBuffer())->appendInt16($short));
    }

    public function writeInt(int $integer): void
    {
        $this->writeBytes((new ByteBuffer())->appendInt32($integer));
    }

    public function writeLong(int $long): void
    {
        $this->writeBytes((new ByteBuffer())->appendInt64($long));
    }

    public function writeVarString(string $value): void
    {
        $this->writeByte(\strlen($value));
        $this->writeBytes($value);
    }
}
