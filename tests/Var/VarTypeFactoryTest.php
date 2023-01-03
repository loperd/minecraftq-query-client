<?php

namespace Loper\Tests\Var;

use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Var\VarTypeFactory;
use PHPUnit\Framework\TestCase;

final class VarTypeFactoryTest extends TestCase
{
    /**
     * @dataProvider intVarProvider
     */
    public function test_create_var_int($randomInt): void
    {
        $buffer = VarTypeFactory::createVarInt($randomInt);
        $is = new ByteBufferInputStream($buffer);

        self::assertEquals($is->readVarInt(), $randomInt);
    }

    public function intVarProvider(): array
    {
        return [
            [1],
            [5],
            [25],
            [50],
            [66],
            [102],
            [177],
            [201],
            [255],
            [\random_int(0, 255)],
            [\random_int(0, 255)],
            [\random_int(0, 255)],
        ];
    }
}
