<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

final class SocketWriteException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Can not write to socket.");
    }
}
