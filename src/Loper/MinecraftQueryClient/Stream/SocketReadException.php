<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

final class SocketReadException extends SocketException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function fromSocketError(string $message, int $code): SocketReadException
    {
        return new self(\sprintf('Cannot read data from socket: [%s] %d', $message, $code));
    }

    public static function couldNotReadBytes(): SocketReadException
    {
        return new self('Could not read bytes.');
    }
}
