<?php

declare(strict_types=1);

namespace Address;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use PHPUnit\Framework\TestCase;

class ServerAddressResolverTest extends TestCase
{
    /**
     * @dataProvider serverAddressProvider
     */
    public function test_resolve_ip_address($host, $address): void
    {
        $serverAddress = ServerAddressResolver::resolve($host);

        self::assertEquals($address, $serverAddress->address);
    }


    public function test_failed_resolve_ip_address(): void  // ?????
    {
        $serverAddress = ServerAddressResolver::resolve("failed string");

        self::assertEquals("failed string", $serverAddress->address);
    }

    public function serverAddressProvider(): array
    {
        return [
            ['a4craft.top', '51.91.214.17'],
            ['65.109.108.27', '65.109.108.27'],
            ['mc.playmine.org', '162.19.190.31'],
            ['mc.musteryworld.net', '51.75.186.98'],
            ['tensa.co.ua', '95.217.119.207'],
        ];
    }
}
