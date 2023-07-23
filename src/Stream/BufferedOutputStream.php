<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Stream;

use PHPinnacle\Buffer\ByteBuffer;

interface BufferedOutputStream extends OutputStream
{
    public function getBuffer(): ByteBuffer;
}
