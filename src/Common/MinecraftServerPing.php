<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common;

use Loper\MinecraftQueryClient\Address\ServerAddress;

interface MinecraftServerPing
{
    public function ping(ServerAddress $serverAddress): ServerPingResult;
}
