<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

final class InvalidServerVersionException extends MinecraftQueryException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function emptyVersion(): self
    {
        return new self('Version cannot be empty.');
    }

    public static function invalidFormat(string $version): self
    {
        return new self(\sprintf('Invalid version format "%s".', $version));
    }

    public static function unableToParse(string $version): self
    {
        return new self(\sprintf('Unable to parse the server version "%s".', $version));
    }
}
