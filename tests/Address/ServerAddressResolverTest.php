<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Address;

use Loper\MinecraftQueryClient\Address\ServerAddressResolver;
use Loper\MinecraftQueryClient\Exception\ServerAddressResolveException;
use PHPUnit\Framework\TestCase;

final class ServerAddressResolverTest extends TestCase
{
    /**
     * @dataProvider serverBadAddressProvider
     */
    public function test_failed_resolve_ip_address($host): void
    {
        $this->expectException(ServerAddressResolveException::class);
        $this->expectExceptionMessage('Cannot resolve address.');

        ServerAddressResolver::resolve($host);
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
}
