<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

final class SocketReadException extends \RuntimeException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct(\sprintf('Cannot read data from socket: [%s] %s', $message, $code));
    }
}
