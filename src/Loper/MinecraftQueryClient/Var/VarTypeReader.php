<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Var;

use Loper\MinecraftQueryClient\Stream\InputStream;

final class VarTypeReader
{
    public static function readVarInt(InputStream $is, int $maxBytes): int
    {
        for ($out = 0, $bytes = 0; ;) {
            $in = $is->readByte();
            $out |= ($in & 0x7F) << ($bytes++ * 7);

            if ($bytes > $maxBytes) {
                throw new \RuntimeException('VarInt too big');
            }

            if (($in & 0x80) !== 0x80) {
                break;
            }
        }

        return $out;
    }
}
