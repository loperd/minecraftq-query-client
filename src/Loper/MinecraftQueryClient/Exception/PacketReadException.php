<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

final class PacketReadException extends \RuntimeException
{
    /**
     * @param class-string $packetClass
     */
    public function __construct(string $packetClass, ?string $details = null)
    {
        $details ??= 'no details';
        parent::__construct(\sprintf('Failed to read packet: "%s". Detail: "%s"', $packetClass, $details));
    }
}
