<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Structure;

final class VersionProtocolMap
{
    public const EXTERNAL = 'e';
    public const INTERNAL = 'i';

    /** @var array<array<array-key, ProtocolVersion|ServerVersion>> */
    public static array $map = [
        [
            self::EXTERNAL => ServerVersion::JAVA_1_19,
            self::INTERNAL => ProtocolVersion::JAVA_1_19,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_18_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_18_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_18_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_18_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_18,
            self::INTERNAL => ProtocolVersion::JAVA_1_18_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_17_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_17_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_17,
            self::INTERNAL => ProtocolVersion::JAVA_1_17,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_16_5,
            self::INTERNAL => ProtocolVersion::JAVA_1_16_5,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_16_4,
            self::INTERNAL => ProtocolVersion::JAVA_1_16_5,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_16_3,
            self::INTERNAL => ProtocolVersion::JAVA_1_16_3,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_16_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_16_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_16_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_16_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_16,
            self::INTERNAL => ProtocolVersion::JAVA_1_16,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_15_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_15_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_15_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_15_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_15,
            self::INTERNAL => ProtocolVersion::JAVA_1_15,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_14_4,
            self::INTERNAL => ProtocolVersion::JAVA_1_14_4,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_14_3,
            self::INTERNAL => ProtocolVersion::JAVA_1_14_3,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_14_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_14_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_14_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_14_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_14,
            self::INTERNAL => ProtocolVersion::JAVA_1_14,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_13_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_13_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_13_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_13_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_13,
            self::INTERNAL => ProtocolVersion::JAVA_1_13,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_12_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_12_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_12_1,
            self::INTERNAL => ProtocolVersion::JAVA_1_12_1,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_12,
            self::INTERNAL => ProtocolVersion::JAVA_1_12,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_7_5,
            self::INTERNAL => ProtocolVersion::JAVA_1_7_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_7_4,
            self::INTERNAL => ProtocolVersion::JAVA_1_7_2,
        ],
        [
            self::EXTERNAL => ServerVersion::JAVA_1_7_2,
            self::INTERNAL => ProtocolVersion::JAVA_1_7_2,
        ],
    ];

    public static function findByProtocol(ProtocolVersion $protocol): ?ServerVersion
    {
        $filtered = \array_filter(self::$map, static fn (array $data)
            => (string) $data[self::INTERNAL]->value === (string) $protocol->value);

        if (0 === \count($filtered)) {
            return null;
        }

        /** @var ServerVersion */
        return \array_shift($filtered)[self::EXTERNAL];
    }

    public static function getByVersion(ServerVersion $version): ProtocolVersion
    {
        $protocol = self::findByVersion($version);

        if (null === $protocol) {
            $message = 'Could not find protocol by minecraft version: [%s].';
            throw new \RuntimeException(\sprintf($message, $version->value));
        }

        return $protocol;
    }

    public static function getByProtocol(ProtocolVersion $protocol): ServerVersion
    {
        $version = self::findByProtocol($protocol);

        if (null === $version) {
            $message = 'Could not find version by minecraft protocol: [%s].';
            throw new \RuntimeException(\sprintf($message, $protocol->value));
        }

        return $version;
    }

    public static function findByVersion(ServerVersion $version): ?ProtocolVersion
    {
        $filtered = \array_filter(self::$map, static fn (array $data)
            => (string) $data[self::EXTERNAL]->value === $version->value);

        if (0 === \count($filtered)) {
            return null;
        }

        /** @var ProtocolVersion */
        return \array_shift($filtered)[self::INTERNAL];
    }
}
