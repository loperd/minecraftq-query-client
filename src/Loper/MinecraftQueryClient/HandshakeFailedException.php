<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

final class HandshakeFailedException extends \RuntimeException
{
    public function __construct(string $detail)
    {
        parent::__construct(\sprintf('Failed handshake: "%s"', $detail));
    }
}
