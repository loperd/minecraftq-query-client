<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Loper\MinecraftQueryClient\Structure\ServerVersion;

final class MappingException extends MinecraftQueryException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function notFoundByVersion(ServerVersion $version): self
    {
        return new self(\sprintf('Could not find protocol by minecraft version: [%s].', $version->value));
    }

    public static function notFoundByProtocol(ProtocolVersion $protocol): self
    {
        return new self(\sprintf('Could not find version by minecraft protocol: [%s].', $protocol->value));
    }
}
