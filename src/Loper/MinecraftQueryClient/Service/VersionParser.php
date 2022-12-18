<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Service;

use Composer\Semver\Semver;
use Loper\MinecraftQueryClient\Exception\InvalidServerVersionException;
use Loper\MinecraftQueryClient\Structure\ServerVersion;

final class VersionParser
{
    public static function parse(string $version): ServerVersion
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
                => VersionParser::processVersion($ver), $parts);
            $result = Semver::rsort($parts);

            return self::getBiggest($result[0]);
        }

        throw InvalidServerVersionException::invalidFormat($version);
    }

    private static function getBiggest(string $version): ServerVersion
    {
        $subVersions = [];
        foreach (ServerVersion::cases() as $case) {
            if (\str_starts_with($case->value, $version)) {
                $subVersions[] = $case->value;
            }
        }

        if (0 === \count($subVersions)) {
            throw InvalidServerVersionException::unableToParse($version);
        }

        if (1 === \count($subVersions)) {
            return ServerVersion::from($subVersions[0]);
        }

        $subVersions = Semver::rsort($subVersions);
        return ServerVersion::from($subVersions[0]);
    }

    public static function getServerVersion(string $version): ServerVersion
    {
        if (\str_contains($version, '.x') && 1 === \preg_match('/^\d\.\d{1,2}\.x$/', $version)) {
            return self::getBiggest(\mb_substr($version, 0, -2));
        }

        if (1 === \preg_match('/^\d\.\d{1,2}(\.\d)?$/', $version)) {
            return ServerVersion::from($version);
        }

        throw InvalidServerVersionException::unableToParse($version);
    }

    public static function processVersion(string $version): string
    {
        return 1 !== \preg_match('/^\d\.\d{1,2}\.x$/', $version)
            ? $version
            : \mb_substr($version, 0, -2);
    }
}
