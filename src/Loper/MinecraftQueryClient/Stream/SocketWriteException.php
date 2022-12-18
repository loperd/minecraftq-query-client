<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

final class SocketWriteException extends SocketException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function unableToWrite(): self
    {
        return new self('Could not write to socket.');
    }
}
