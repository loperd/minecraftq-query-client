<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java\Provider;

use Loper\MinecraftQueryClient\Response\ServerStatisticsResponse;

interface ServerStatisticsProvider
{
    public function getStatistics(): ServerStatisticsResponse;
}
