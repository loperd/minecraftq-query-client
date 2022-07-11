<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common\Exception;

use Loper\MinecraftQueryClient\Address\ServerAddress;

final class SocketConnectionException extends \RuntimeException
{
    private ServerAddress $serverAddress;

    public function __construct(ServerAddress $serverAddress, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('Could not connect to the server: "%s"', $serverAddress), 0, $previous);

        $this->serverAddress = $serverAddress;
    }

    public function getServerAddress(): ServerAddress
    {
        return $this->serverAddress;
    }
}
