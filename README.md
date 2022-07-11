
# Minecraft Query Client

Simple implementation of [Server List Ping](https://wiki.vg/Server_List_Ping) and [Query](https://wiki.vg/Query) for getting status of Minecraft Server.

### Installation

```bash
composer require loper/minecraft-query-client
```

### Examples

#### Server List Ping
examples/ping.php

```php
declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$host = 'go.mon.pe';
$port = 25565;

// Resolve host by domain and SRV records if it exists
$address = ServerAddressResolver::resolve($host, $port);
$client = MinecraftClientFactory::createJavaClient($address, 1.5);

var_dump($client->getStats());
```

#### Query
examples/query.php

```php
<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$host = 'go.mon.pe';
$port = 25565;

// Resolve host by domain and SRV records if it exists
$address = ServerAddressResolver::resolve($host, $port);
$client = MinecraftClientFactory::createQueryClient($address, 1.5);

var_dump($client->getStats());
```

#### Both
examples/both.php

```php
<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;use Loper\MinecraftQueryClient\MinecraftClientFactory;

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
$client = MinecraftClientFactory::createBothQueryClient($address, 1.5);

var_dump($client->getStats());
```

### Credits

- [PHP-Minecraft-Query](https://github.com/xPaw/PHP-Minecraft-Query)
- [BotFilter by Leymooo](https://github.com/Leymooo/BungeeCord)
