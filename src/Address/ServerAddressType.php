<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Address;

enum ServerAddressType
{
    case SrvMapped;
    case Dedicated;
    case Shared;
}
