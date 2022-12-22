<?php

declare(strict_types=1);

namespace Address;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use PHPUnit\Framework\TestCase;

class ServerAddressTest extends TestCase
{

    /**
     *
     * @dataProvider serverAddressProvider
     *
     */
    public function test_server_address_formater($host, $address, $port, $formatedAddress): void
    {
        $serverAddress = new ServerAddress($host, $address, $port);

        self::assertEquals($formatedAddress, $serverAddress->format());
    }

    public function test_failed_server_address_formater(): void // ?????
    {
        $serverAddress = new ServerAddress("failed address", "failed address");

        self::assertEquals('failed address:25565', $serverAddress->format());
    }

    /**
     *
     * @dataProvider serverAddressProvider
     *
     */
    public function test_get_server_address_as_string($host, $address, $port, $formatedAddress): void
    {
        $serverAddress = new ServerAddress($host, $address, $port);

        self::assertEquals($formatedAddress, (string)$serverAddress);
    }

    public function serverAddressProvider(): array
    {
        return [
          ['a4craft.top', '51.91.214.17', null, '51.91.214.17:25565'],
          ['a4craft.top', '51.91.214.17', 25655, '51.91.214.17:25655'],
          ['mc.mineblaze.net', 'mc.mineblaze.net', 25577, 'mc.mineblaze.net:25577'],
          ['mc.mineblaze.net', 'mc.mineblaze.net', null, 'mc.mineblaze.net:25565']
        ];
    }

}
