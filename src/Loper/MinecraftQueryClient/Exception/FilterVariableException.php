<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

final class FilterVariableException extends MinecraftQueryException
{
    public function __construct(string $input)
    {
        parent::__construct(\sprintf('Failed to filter variable: "%s"', $input));
    }
}