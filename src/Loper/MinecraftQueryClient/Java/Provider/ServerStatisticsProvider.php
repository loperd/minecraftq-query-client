<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Provider;

use Loper\MinecraftQueryClient\Java\ServerStatisticsResponse;

interface ServerStatisticsProvider
{
    public function getStatistics(): ServerStatisticsResponse;
}
