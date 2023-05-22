<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

final class InvalidPortException extends MinecraftQueryException
{
    private function __construct(string $message, public readonly int $portValue)
    {
        parent::__construct($message);
    }

    public static function shouldBeUnsigned(int $portValue): self
    {
        return new self(\sprintf('Expected unsigned integer port value, but actual is "%d"', $portValue), $portValue);
    }
}
