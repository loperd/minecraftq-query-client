<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Java;

use Loper\MinecraftQueryClient\Structure\ProtocolVersion;
use Loper\MinecraftQueryClient\Structure\ServerVersion;

final class ServerStatisticsResponse
{
    /** @var string[] */
    public array $plugins = [];

    public ?string $map = null;
    public ?ServerVersion $version = null;
    public ?string $host = null;

    public ?int $port = null;
    public int $numPlayers;
    public int $maxPlayers;

    /** @var string[] */
    public array $players = [];
    public ?string $motd = null;
    public ?string $rawMotd = null;
    public ?string $serverSoftware = null;

    public ProtocolVersion $protocol;
}
