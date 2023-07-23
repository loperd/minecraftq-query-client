
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
#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: JavaProtocolVersion::JAVA_1_7_1,
        timeout: 1.5
    );
    $javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

    $provider = $javaMinecraftProviderFactory->createPingStatisticsProvider();

    var_dump($provider->getStatistics());
};

```

#### Query
examples/java-query.php

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: JavaProtocolVersion::JAVA_1_7_1,
        timeout: 1.5
    );
    $javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

    $provider = $javaMinecraftProviderFactory->createQueryStatisticsProvider();

    var_dump($provider->getStatistics());
};

```

#### Both
examples/java-both.php

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Java\JavaStatisticsProviderFactory;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: JavaProtocolVersion::JAVA_1_7_1,
        timeout: 1.5
    );
    $javaMinecraftProviderFactory = new JavaStatisticsProviderFactory($minecraftClientFactory);

    $provider = $javaMinecraftProviderFactory->createCommonStatisticsProvider();

    var_dump($provider->getStatistics());
};
```

#### Bedrock
examples/bedrock-ping.php

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Bedrock\PingServerStatisticsProvider;
use Loper\MinecraftQueryClient\MinecraftClientFactory;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    $address = ServerAddressResolver::resolve($host, $port);

    $minecraftClientFactory = new MinecraftClientFactory(
        serverAddress: $address,
        protocol: BedrockProtocolVersion::BEDROCK_1_20_1,
        timeout: 1.5
    );

    $provider = new PingServerStatisticsProvider(
        $minecraftClientFactory->createBedrockClient()
    );

    var_dump($provider->getStatistics());
};
```

### Credits

- [Wiki.vg](https://wiki.vg/Main_Page)
- [Minecraft Fandom Wiki](https://minecraft.fandom.com/wiki/Protocol_version)
- [PHP-Minecraft-Query](https://github.com/xPaw/PHP-Minecraft-Query)
- [BotFilter by Leymooo](https://github.com/Leymooo/BungeeCord)
- [Bungeecord](https://github.com/SpigotMC/BungeeCord)
