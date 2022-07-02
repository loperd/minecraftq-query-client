<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Structure;

enum ProtocolVersion: int
{
    case VER_1_19 = 759;
    case VER_1_18_2 = 758;
    case VER_1_18_1 = 757;
    case VER_1_17_1 = 756;
    case VER_1_17 = 755;
    case VER_1_16_5 = 754;
    case VER_1_16_3 = 753;
    case VER_1_16_2 = 751;
    case VER_1_16_1 = 736;
    case VER_1_16 = 735;
    case VER_1_15_2 = 578;
    case VER_1_15_1 = 575;
    case VER_1_15 = 573;
    case VER_1_14_4 = 498;
    case VER_1_14_3 = 490;
    case VER_1_14_2 = 485;
    case VER_1_14_1 = 480;
    case VER_1_14 = 477;
    case VER_1_13_2 = 404;
    case VER_1_13_1 = 401;
    case VER_1_13 = 393;
    case VER_1_12_2 = 340;
    case VER_1_12_1 = 338;
    case VER_1_12 = 335;
    case VER_1_7_2 = 4;
}
