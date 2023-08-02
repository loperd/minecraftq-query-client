<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use Composer\Semver\Semver;
use Loper\Minecraft\Protocol\Struct\JavaServerVersion;
use Loper\MinecraftQueryClient\Exception\InvalidServerVersionException;

final class JavaVersionParser
{
    public static function parse(string $version): JavaServerVersion
    {
        if ('' === $version) {
            throw InvalidServerVersionException::emptyVersion();
        }

        if (6 >= mb_strlen($version)) {
            return self::getServerVersion($version);
        }

        if (\str_contains($version, ', ')) {
            $parts = \explode(', ', $version);
            $parts = \array_map(static fn (string $ver): string
                => JavaVersionParser::processVersion($ver), $parts);
            $result = Semver::rsort($parts);

            return self::getBiggest($result[0]);
        }

        throw InvalidServerVersionException::invalidFormat($version);
    }

    private static function getBiggest(string $version): JavaServerVersion
    {
        $subVersions = [];
        foreach (JavaServerVersion::cases() as $case) {
            if (\str_starts_with($case->value, $version)) {
                $subVersions[] = self::processVersion($case->value);
            }
        }

        if (0 === \count($subVersions)) {
            throw InvalidServerVersionException::unableToParse($version);
        }

        if (1 === \count($subVersions)) {
            return JavaServerVersion::from($subVersions[0]);
        }

        $subVersions = Semver::rsort(array_unique($subVersions));
        return JavaServerVersion::from($subVersions[0]);
    }

    public static function getServerVersion(string $version): JavaServerVersion
    {
        if (\str_contains($version, '.x') && 1 === \preg_match('/^\d\.\d{1,2}\.x$/', $version)) {
            return self::getBiggest(\mb_substr($version, 0, -2));
        }

        if (1 === \preg_match('/^\d\.\d{1,2}(\.\d)?$/', $version)) {
            return JavaServerVersion::from($version);
        }

        throw InvalidServerVersionException::unableToParse($version);
    }

    public static function processVersion(string $version): string
    {
        if (1 === \preg_match('/^\d\.\d{1,2}\.x$/', $version)) {
            return \mb_substr($version, 0, -2);
        }

        if (1 === \preg_match('/^(\d+(?:\.\d{1,2})+)(?:-[a-z]+)+-?(?:\d+)?$/', $version, $matches)) {
            return $matches[1];
        }

        return $version;
    }
}
