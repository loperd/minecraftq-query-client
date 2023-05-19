<?php

declare(strict_types=1);

namespace Loper\Tests\Var;

use Loper\MinecraftQueryClient\Var\VarMotdFilter;
use PHPUnit\Framework\TestCase;

final class VarMotdFilterTest extends TestCase
{
    /**
     * @dataProvider motdDataProvider
     */
    public function test_filter_motd(string $inputMotd, string $expectedMotd): void
    {
        self::assertEquals($expectedMotd, VarMotdFilter::filter($inputMotd));
    }

    public function motdDataProvider(): array
    {
        return [
            [
                '§3§k§l||§r §c§lMi§e§lne§b§lla§a§lnd §6§lNetwork§r §f»§r §f1.8-1.19.4§r   §f[§f§lPvP§f §f§l1.8§f]§r
§f    §d§l§nCREATIVE§b§l+§8 ◂ §e§l§nBW§8 ▪ §b§l§nSW§8 ▪ §a§l§nSKYBLOCK§8 ▸ §c§l§nЕЩЁ!§8',
                ' Mineland Network 1.8-1.19.4 PvP 1.8 CREATIVE+ BW SW SKYBLOCK ЕЩЁ!',
            ],
            [
                '§3§l||§r §c§lПшоноКрафт§r §f»§r §f1.8-1.18.1§r   §e§lПерший Український Майнкрафт Сервер',
                ' ПшоноКрафт 1.8-1.18.1 Перший Український Майнкрафт Сервер',
            ],
        ];
    }
}