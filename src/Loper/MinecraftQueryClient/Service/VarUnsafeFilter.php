<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Service;

final class VarUnsafeFilter
{
    public static function filter(string $input): string
    {
        $result = \filter_var($input, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW|FILTER_FLAG_STRIP_HIGH);

        if (false === $result) {
            throw new \RuntimeException(\sprintf('Failed to filter variable: "%s"', $input));
        }

        return $result;
    }
}
