<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

interface MinecraftQueryClient
{
    public function getStats(): ServerStatsResponse;

    public function close(): void;
}
