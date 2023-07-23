<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use PHPinnacle\Buffer\ByteBuffer;

interface InputStream
{
    public const MAX_READ_BYTES = 1_024_000;

    public function readFullData(int $maxReadBytes = self::MAX_READ_BYTES): ByteBuffer;

    public function readString(): ByteBuffer;

    public function readVarInt(int $maxBytes = 5): int;

    public function readVarShort(): int;

    public function readBytes(int $size): ByteBuffer;

    public function readByte(): int;

    public function readShort(): int;

    public function readInt(): int;

    public function readLong(): int;

    public function readUnsignedShort(): int;

    public function readLittleEndianUnsignedShort(): int;

    public function readUnsignedByte(): int;
}
