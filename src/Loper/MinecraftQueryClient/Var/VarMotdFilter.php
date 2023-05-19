<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Var;

final class VarMotdFilter
{
    public const FILTER_SYMBOLS_REGEX = '/[^0-9A-Za-zА-Яа-яЁёЇїЪъЭэЄє.:;!?#"$%() *\+,\/\-=\^_<>]/u';

    public static function filter(string $motd, string $regex = self::FILTER_SYMBOLS_REGEX): string
    {
        if ('' === $motd) {
            return $motd;
        }

        $text = preg_replace('/(&|§|\\\u00A7)./u', '', $motd);
        $text = transliterator_transliterate('Hex-Any/Java', $text);
        $text = preg_replace($regex, '', $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);

        return preg_replace('/^\s+([!?\.])/', '$1', $text);
    }
}