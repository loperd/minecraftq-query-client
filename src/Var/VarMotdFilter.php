<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Var;

final class VarMotdFilter
{
    public const TRIM_SYMBOLS = '|\\\/-_$@~%^&*';
    public const TRIM_SYMBOLS_WITH_SPACES = self::TRIM_SYMBOLS . ' ';

    public static function filter(string $input, ?string $trimSymbols = null, ?string $replaceRegex = null): string
    {
        if ('' === $input) {
            return $input;
        }

        $value = false;
        if (mb_check_encoding($input, 'UTF-8')) {
            $value = mb_convert_encoding($input, 'UTF-8');
        }

        if (false === $value || !is_string($value)) {
            return '';
        }

        $text = (string) preg_replace('/(&|§|\\\u00A7)./u', '', $value);
        $text = (string) transliterator_transliterate('Hex-Any/Java', $text);
        $text = (string) preg_replace('/\s{2,}|\n+/', ' ', $text);
        $text = (string) preg_replace('/^\s+([!?.])/', '$1', $text);

        if (null !== $replaceRegex) {
            $text = preg_replace($replaceRegex, '', $text);
        }

        return null === $trimSymbols ? $text : trim($text, $trimSymbols);
    }
}
