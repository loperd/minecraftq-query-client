<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Var;

use PHPinnacle\Buffer\ByteBuffer;

final class VarTypeFactory
{
    public static function createVarInt(int $data): ByteBuffer
    {
        $buffer = new ByteBuffer();

        while (true) {
            $temp = $data & 0x7F;
            $data >>= 7;

            if ($data !== 0) {
                $temp |= 0x80;
            }

            $buffer->appendUint8($temp);

            if ($data === 0) {
                break;
            }
        }

        if (0 === $buffer->size()) {
            throw new \RuntimeException('Buffer size is 0.');
        }

        return $buffer;
    }
}
