
# Minecraft Query Client

Simple implementation of [Server List Ping](https://wiki.vg/Server_List_Ping) and [Query](https://wiki.vg/Query) for getting status of Minecraft Server.

### Installation

```bash
composer require loperd/minecraft-query-client
```

### Examples

#### Server List Ping
examples/ping.php
```php
declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$host = 'go.mon.pe';
$port = 25565;

// Resolve host by domain and SRV records if it exists
$address = ServerAddressResolver::resolve($host, $port);
$client = MinecraftClientFactory::createTCPQueryClient($address, 1.5);

var_dump($client->getStats());
```

#### Query
examples/query.php
```php<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$host = 'go.mon.pe';
$port = 25565;

// Resolve host by domain and SRV records if it exists
$address = ServerAddressResolver::resolve($host, $port);
$client = MinecraftClientFactory::createUDPQueryClient($address, 1.5);

var_dump($client->getStats());
```

### Credits

- [PHP-Minecraft-Query](https://github.com/xPaw/PHP-Minecraft-Query)
- [BotFilter by Leymooo](https://github.com/Leymooo/BungeeCord)