<?php

namespace Service;

use Loper\MinecraftQueryClient\Var\VarTypeFactory;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

class VarTypeFactoryTest extends TestCase
{

    /**
     * @dataProvider intVarProvider
     */
    public function test_create_var_int($randomInt): void
    {
        $buffer = VarTypeFactory::createVarInt($randomInt);

        self::assertEquals($buffer->readUint8(), $randomInt);
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
