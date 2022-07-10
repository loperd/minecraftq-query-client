<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

interface MinecraftClient
{
    public function getStats(): ServerStatsResponse;

    public function close(): void;
}
