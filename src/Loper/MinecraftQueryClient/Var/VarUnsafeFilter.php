<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Var;

use Loper\MinecraftQueryClient\Exception\FilterVariableException;

final class VarUnsafeFilter
{
    public static function filter(string $input): string
    {
        $result = \filter_var($input, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

        if (false === $result) {
            throw new FilterVariableException($input);
        }

        return $result;
    }

    public static function filterText(string $input): string
    {
        $result = \filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        if (false === $result) {
            throw new FilterVariableException($input);
        }

        return \preg_replace('/(\s)+/', '$1', \strip_tags($result));
    }
}
