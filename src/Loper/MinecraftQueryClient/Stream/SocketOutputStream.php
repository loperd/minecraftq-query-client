<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use Loper\MinecraftQueryClient\Var\VarTypeFactory;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw as Socket;

final class SocketOutputStream implements OutputStream
{
    private Socket\Socket $socket;

    public function __construct(Socket\Socket $socket)
    {
        $this->socket = $socket;
    }

    public function writeBytes(ByteBuffer|string $bytes): void
    {
        $buffer = (string) $bytes;

        if ('' === $buffer) {
            throw new \InvalidArgumentException('Can not write empty value.');
        }

        if (0 === $this->socket->write($buffer)) {
            throw new SocketWriteException();
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
