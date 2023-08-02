<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use PHPinnacle\Buffer\ByteBuffer;

final class ArrayByteBufferConverter
{
    /**
     * @param int[] $bytes
     * @return ByteBuffer
     */
    public static function convert(array $bytes): ByteBuffer
    {
        $buffer = new ByteBuffer();

        foreach ($bytes as $byte) {
            $buffer->appendInt8($byte);
        }

        return $buffer;
    }
}
