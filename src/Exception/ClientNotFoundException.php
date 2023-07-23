<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Exception;

use Loper\MinecraftQueryClient\MinecraftClient;

final class ClientNotFoundException extends MinecraftQueryException
{
    public function __construct()
    {
        parent::__construct(\sprintf('Client implementations of "%s" not found.', MinecraftClient::class));
    }
}
