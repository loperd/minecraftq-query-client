<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Helper;

use PHPinnacle\Buffer\ByteBuffer;

final class PacketFactory
{
    public static function createBasicStatBuffer(string $motd, string $smp, string $world, string $numPlayers, string $maxPlayers, string $host = '127.0.1.1'): ByteBuffer
    {
        $buffer = new ByteBuffer();
        $buffer->appendInt32(1);
        $buffer->appendInt8(1);
        $buffer->append($motd);
        $buffer->append("\x0");
        $buffer->append($smp);
        $buffer->append("\x0");
        $buffer->append($world);
        $buffer->append("\x0");
        $buffer->append($numPlayers);
        $buffer->append("\x0");
        $buffer->append($maxPlayers);
        $buffer->append("\x0");
        $buffer->append(base64_decode("GQA=", true));
        $buffer->append($host);
        $buffer->append("\x0");

        return $buffer;
    }

    public static function createHandshakeBuffer(int $sessionId, string $token): ByteBuffer
    {
        $buffer = new ByteBuffer();
        $buffer->appendInt32($sessionId);
        $buffer->appendInt8(1);
        $buffer->append($token);

        return $buffer;
    }
}
