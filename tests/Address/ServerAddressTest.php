<?php

declare(strict_types=1);

namespace Loper\Tests\Address;

use Loper\MinecraftQueryClient\Address\ServerAddress;
use Loper\MinecraftQueryClient\Exception\ServerAddressResolveException;
use PHPUnit\Framework\TestCase;

final class ServerAddressTest extends TestCase
{
    /**
     *
     * @dataProvider serverAddressProvider
     *
     */
    public function test_server_address_formatter($host, $address, $port, $formattedAddress): void
    {
        $serverAddress = new ServerAddress($host, $address, $port);


        self::assertEquals($formattedAddress, $serverAddress->format());
    }

    /**
     *
     * @dataProvider serverBadAddressProvider
     *
     */
    public function test_failed_server_address_formatter($host): void
    {
        $this->expectException(ServerAddressResolveException::class);
        $this->expectExceptionMessage('Cannot resolve address.');

        new ServerAddress($host, $host);
    }

    public function serverBadAddressProvider(): array
    {
        return [
            ['example com'],
            ['65.109.108.27.0'],
            ['example@com.ua'],
            ['example--lan.com'],
            ['example.com/'],
            ['<a> example.com </a>'],
        ];
    }
    /**
     *
     * @dataProvider serverAddressProvider
     *
     */
    public function test_get_server_address_as_string($host, $address, $port, $formattedAddress): void
    {
        $serverAddress = new ServerAddress($host, $address, $port);

        self::assertEquals($formattedAddress, (string)$serverAddress);
    }

    public function serverAddressProvider(): array
    {
        return [
          ['example.com', '51.91.214.17', null, '51.91.214.17:25565'],
          ['example.com', '51.91.214.17', 25655, '51.91.214.17:25655'],
        ];
    }
}
