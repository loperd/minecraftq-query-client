
# Minecraft Query Client

Simple implementation of [Server List Ping](https://wiki.vg/Server_List_Ping) and [Query](https://wiki.vg/Query) for getting status of Minecraft Server.

### Installation

```bash
composer require loper/minecraft-query-client
```

### Examples

#### Server List Ping
examples/java-ping.php

```php
<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

require_once __DIR__ . '/../vendor/autoload.php';

$host = $argv[1] ?? null;
$port = $argv[2] ?? 25565;

if (!isset($host)) {
    echo PHP_EOL;
    \printf("Usage: php %s <host> <port>\n", $_SERVER['SCRIPT_FILENAME']);
    echo PHP_EOL;
    exit;
}

$address = ServerAddressResolver::resolve($host, $port);

$minecraftClientFactory = new MinecraftClientFactory(
    serverAddress: $address,
    protocol: ProtocolVersion::JAVA_1_7_2,
    timeout: 1.5);
$javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

$provider = $javaMinecraftProviderFactory->createPingStatisticsProvider();

var_dump($provider->getStatistics());


```

#### Query
examples/java-query.php

```php
<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

require_once __DIR__ . '/../vendor/autoload.php';

$host = $argv[1] ?? null;
$port = $argv[2] ?? 25565;

if (!isset($host)) {
    echo PHP_EOL;
    \printf("Usage: php %s <host> <port>\n", $_SERVER['SCRIPT_FILENAME']);
    echo PHP_EOL;
    exit;
}

$address = ServerAddressResolver::resolve($host, $port);

$minecraftClientFactory = new MinecraftClientFactory(
    serverAddress: $address,
    protocol: ProtocolVersion::JAVA_1_7_2,
    timeout: 1.5);
$javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

$provider = $javaMinecraftProviderFactory->createQueryStatisticsProvider();

var_dump($provider->getStatistics());

```

#### Both
examples/java-both.php

```php
<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;
use Loper\MinecraftQueryClient\Structure\ProtocolVersion;

require_once __DIR__ . '/../vendor/autoload.php';

$host = $argv[1] ?? null;
$port = $argv[2] ?? 25565;

if (!isset($host)) {
    echo PHP_EOL;
    \printf("Usage: php %s <host> <port>\n", $_SERVER['SCRIPT_FILENAME']);
    echo PHP_EOL;
    exit;
}

$address = ServerAddressResolver::resolve($host, $port);

$minecraftClientFactory = new MinecraftClientFactory(
    serverAddress: $address,
    protocol: ProtocolVersion::JAVA_1_7_2,
    timeout: 1.5);
$javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

$provider = $javaMinecraftProviderFactory->createCommonStatisticsProvider();

var_dump($provider->getStatistics());

```

### Credits

- [PHP-Minecraft-Query](https://github.com/xPaw/PHP-Minecraft-Query)
- [BotFilter by Leymooo](https://github.com/Leymooo/BungeeCord)
