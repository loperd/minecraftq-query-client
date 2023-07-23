<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

final class VarTypeReaderException extends MinecraftQueryException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function varIntTooBig(): self
    {
        return new self('VarInt too big');
    }
}
