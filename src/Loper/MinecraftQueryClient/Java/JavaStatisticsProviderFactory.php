<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use Loper\MinecraftQueryClient\Java\Provider\CommonServerStatisticsProvider;
use Loper\MinecraftQueryClient\Java\Provider\PingServerStatisticsProvider;
use Loper\MinecraftQueryClient\Java\Provider\QueryServerStatisticsProvider;
use Loper\MinecraftQueryClient\MinecraftClientFactory;
use Loper\MinecraftQueryClient\Stream\SocketConnectionException;

final class JavaStatisticsProviderFactory
{
    public function __construct(private readonly MinecraftClientFactory $minecraftClientFactory)
    {
    }

    public function createPingStatisticsProvider(): PingServerStatisticsProvider
    {
        return new PingServerStatisticsProvider($this->minecraftClientFactory->createJavaClient());
    }

    public function createQueryStatisticsProvider(): QueryServerStatisticsProvider
    {
        return new QueryServerStatisticsProvider($this->minecraftClientFactory->createQueryClient());
    }

    public function createCommonStatisticsProvider(): CommonServerStatisticsProvider
    {
        try {
            $queryClient = $this->minecraftClientFactory->createQueryClient();
        } catch (SocketConnectionException) {
            $queryClient = null;
        }

        try {
            $javaClient = $this->minecraftClientFactory->createJavaClient();
        } catch (SocketConnectionException) {
            $javaClient = null;
        }

        return new CommonServerStatisticsProvider($javaClient, $queryClient);
    }
}