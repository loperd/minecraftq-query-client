<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

use Loper\MinecraftQueryClient\Address\ServerAddress;

final class PacketSendException extends \RuntimeException
{
    /**
     * @param class-string $packetClass
     */
    public function __construct(string $packetClass, ServerAddress $address, ?\Throwable $previous = null)
    {
        $message = \sprintf('Failed to send packet "%s": [%s] .', $packetClass, $address);

        parent::__construct($message, 0, $previous);
    }
}