
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
use Loper\MinecraftQueryClient\MinecraftQuery;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    var_dump(MinecraftQuery::javaPing(
        host: $host,
        port: $port,
        protocol: JavaProtocolVersion::JAVA_1_7_1
    ));
};
```

#### Query
examples/java-query.php

```php
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
```

#### Aggregated result from Query protocol & Minecraft server ping
examples/java-both.php

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use Loper\Minecraft\Protocol\Struct\JavaProtocolVersion;
use Loper\MinecraftQueryClient\MinecraftQuery;

require_once __DIR__ . '/bootstrap.php';

return static function (string $host, int $port) {
    var_dump(MinecraftQuery::javaQueryPing(
        host: $host,
        port: $port,
        protocol: JavaProtocolVersion::JAVA_1_7_1
    ));
};
```

#### Bedrock
examples/bedrock-ping.php

```php
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
            protocol: BedrockProtocolVersion::BEDROCK_1_20_1
    ));
};
```

### Credits

- [Wiki.vg](https://wiki.vg/Main_Page)
- [Minecraft Fandom Wiki](https://minecraft.fandom.com/wiki/Protocol_version)
- [PHP-Minecraft-Query](https://github.com/xPaw/PHP-Minecraft-Query)
- [BotFilter by Leymooo](https://github.com/Leymooo/BungeeCord)
- [Bungeecord](https://github.com/SpigotMC/BungeeCord)
