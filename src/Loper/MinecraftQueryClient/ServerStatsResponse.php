<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient;

final class ServerStatsResponse
{
    /** @var string[] */
    public array $plugins = [];

    public ?string $map = null;
    public ?string $version = null;
    public ?string $host = null;

    public ?int $port = null;
    public int $numPlayers;
    public int $maxPlayers;

    /** @var string[] */
    public array $players = [];
    public ?string $motd = null;
    public ?string $serverSoftware = null;
}
