#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\MinecraftQuery;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    var_dump(MinecraftQuery::bedrockPing(
        host: $host,
        port: $port,
        protocol: BedrockProtocolVersion::BEDROCK_1_20_12,
    ));
};
