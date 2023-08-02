#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\MinecraftQuery;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    var_dump(MinecraftQuery::queryPing(
        host: $host,
        port: $port,
        protocol: JavaProtocolVersion::JAVA_1_7_1
    ));
};
