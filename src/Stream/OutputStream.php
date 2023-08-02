<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use PHPinnacle\Buffer\ByteBuffer;

interface OutputStream
{
    /**
     * @param int[]|ByteBuffer|string $bytes
     */
    public function writeBytes(array|ByteBuffer|string $bytes): void;

    public function writeByte(int $byte): void;

    public function writeShort(int $short): void;

    public function writeInt(int $integer): void;

    public function writeLong(int $long): void;

    public function writeVarInt(int $data): void;

    public function writeVarString(string $value): void;
}
