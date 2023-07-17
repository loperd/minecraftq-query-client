<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

use Loper\Minecraft\Protocol\ProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Bedrock\BedrockMinecraftClient;
use Loper\MinecraftQueryClient\Common\Query\QueryMinecraftClient;
use Loper\MinecraftQueryClient\Java\JavaMinecraftClient;

final class MinecraftClientFactory
{
    public function __construct(
        private readonly ServerAddress $serverAddress,
        private readonly ProtocolVersion $protocol,
        private readonly float $timeout
    ) {
    }

    public function createQueryClient(): QueryMinecraftClient
    {
        return new QueryMinecraftClient($this->serverAddress, $this->protocol, $this->timeout);
    }

    public function createJavaClient(): JavaMinecraftClient
    {
        return new JavaMinecraftClient($this->serverAddress, $this->protocol, $this->timeout);
    }

    public function createBedrockClient(): BedrockMinecraftClient
    {
        return new BedrockMinecraftClient($this->serverAddress, $this->protocol, $this->timeout);
    }
}
