<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Var;

use Loper\MinecraftQueryClient\Exception\VarTypeReaderException;
use Loper\MinecraftQueryClient\Stream\InputStream;

final class VarTypeReader
{
    public static function readVarInt(InputStream $is, int $maxBytes): int
    {
        for ($out = 0, $bytes = 0; ;) {
            $in = $is->readByte();
            $out |= ($in & 0x7F) << ($bytes++ * 7);

            if ($bytes > $maxBytes) {
                throw VarTypeReaderException::varIntTooBig();
            }

            if (($in & 0x80) !== 0x80) {
                break;
            }
        }

        return $out;
    }

    public static function readVarShort(InputStream $is): int
    {
        $low = $is->readUnsignedShort();
        $high = 0;
        if (($low & 0x8000) !== 0) {
            $low &= 0x7FFF;
            $high = $is->readUnsignedByte();
        }

        return (($high & 0xFF) << 15) | $low;
    }
}
