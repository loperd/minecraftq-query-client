<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

final class DataSizeException extends SocketException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function tooBigData(): self
    {
        return new self('Too big data.');
    }

    public static function emptyBuffer(): self
    {
        return new self('Buffer is empty.');
    }
}
