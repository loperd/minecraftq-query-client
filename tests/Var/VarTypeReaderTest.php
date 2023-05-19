<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Var;

use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use Loper\MinecraftQueryClient\Var\VarTypeReader;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

final class VarTypeReaderTest extends TestCase
{
    /**
     * @dataProvider intVarProvider
     */
    public function test_read_var_int(int $varInt): void
    {
        $buffer = new ByteBuffer();
        $os = new ByteBufferOutputStream($buffer);
        $os->writeByte($varInt);
        $is = new ByteBufferInputStream($os->getBuffer());
        $out = VarTypeReader::readVarInt($is, $varInt);

        self::assertEquals($out, $varInt);
    }

    /**
     * @dataProvider intVarProvider
     */
    public function test_read_var_short(int $varInt): void
    {
        $buffer = new ByteBuffer();
        $os = new ByteBufferOutputStream($buffer);
        $os->writeShort($varInt);
        $is = new ByteBufferInputStream($os->getBuffer());
        $out = VarTypeReader::readVarShort($is);

        self::assertEquals($out, $varInt);
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
            [127],
            [\random_int(0, 127)],
            [\random_int(0, 127)],
            [\random_int(0, 127)],
        ];
    }

    public function test_failed_read_var_int(): void
    {
        $buffer = new ByteBuffer();
        $os = new ByteBufferOutputStream($buffer);
        $os->writeByte(127);
        $is = new ByteBufferInputStream($os->getBuffer());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('VarInt too big');

        VarTypeReader::readVarInt($is, 0);
    }
}
