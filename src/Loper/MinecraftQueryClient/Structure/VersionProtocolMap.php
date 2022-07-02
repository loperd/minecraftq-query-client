<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Structure;

final class VersionProtocolMap
{
    public const EXTERNAL = 'e';
    public const INTERNAL = 'i';

    /** @var array<array<array-key, ProtocolVersion|MinecraftVersion>> */
    public static array $map = [
        [
            self::EXTERNAL => MinecraftVersion::VER_1_19,
            self::INTERNAL => ProtocolVersion::VER_1_19,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_18_2,
            self::INTERNAL => ProtocolVersion::VER_1_18_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_18_1,
            self::INTERNAL => ProtocolVersion::VER_1_18_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_18,
            self::INTERNAL => ProtocolVersion::VER_1_18_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_17_1,
            self::INTERNAL => ProtocolVersion::VER_1_17_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_17,
            self::INTERNAL => ProtocolVersion::VER_1_17,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_16_5,
            self::INTERNAL => ProtocolVersion::VER_1_16_5,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_16_4,
            self::INTERNAL => ProtocolVersion::VER_1_16_5,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_16_3,
            self::INTERNAL => ProtocolVersion::VER_1_16_3,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_16_2,
            self::INTERNAL => ProtocolVersion::VER_1_16_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_16_1,
            self::INTERNAL => ProtocolVersion::VER_1_16_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_16,
            self::INTERNAL => ProtocolVersion::VER_1_16,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_15_2,
            self::INTERNAL => ProtocolVersion::VER_1_15_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_15_1,
            self::INTERNAL => ProtocolVersion::VER_1_15_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_15,
            self::INTERNAL => ProtocolVersion::VER_1_15,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_14_4,
            self::INTERNAL => ProtocolVersion::VER_1_14_4,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_14_3,
            self::INTERNAL => ProtocolVersion::VER_1_14_3,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_14_2,
            self::INTERNAL => ProtocolVersion::VER_1_14_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_14_1,
            self::INTERNAL => ProtocolVersion::VER_1_14_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_14,
            self::INTERNAL => ProtocolVersion::VER_1_14,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_13_2,
            self::INTERNAL => ProtocolVersion::VER_1_13_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_13_1,
            self::INTERNAL => ProtocolVersion::VER_1_13_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_13,
            self::INTERNAL => ProtocolVersion::VER_1_13,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_12_2,
            self::INTERNAL => ProtocolVersion::VER_1_12_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_12_1,
            self::INTERNAL => ProtocolVersion::VER_1_12_1,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_12,
            self::INTERNAL => ProtocolVersion::VER_1_12,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_7_5,
            self::INTERNAL => ProtocolVersion::VER_1_7_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_7_4,
            self::INTERNAL => ProtocolVersion::VER_1_7_2,
        ],
        [
            self::EXTERNAL => MinecraftVersion::VER_1_7_2,
            self::INTERNAL => ProtocolVersion::VER_1_7_2,
        ],
    ];

    public static function findByProtocol(ProtocolVersion $protocol): ?MinecraftVersion
    {
        $filtered = \array_filter(self::$map, static fn (array $data)
            => (string) $data[self::INTERNAL]->value === (string) $protocol->value);

        if (0 === \count($filtered)) {
            return null;
        }

        /** @var MinecraftVersion */
        return \array_shift($filtered)[self::EXTERNAL];
    }

    public static function findByVersion(MinecraftVersion $protocol): ?ProtocolVersion
    {
        $filtered = \array_filter(self::$map, static fn (array $data)
            => (string) $data[self::EXTERNAL]->value === $protocol->value);

        if (0 === \count($filtered)) {
            return null;
        }

        /** @var ProtocolVersion */
        return \array_shift($filtered)[self::INTERNAL];
    }
}
