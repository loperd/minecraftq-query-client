<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Structure;

enum ProtocolVersion: int
{
    case JAVA_1_19 = 759;
    case JAVA_1_18_2 = 758;
    case JAVA_1_18_1 = 757;
    case JAVA_1_17_1 = 756;
    case JAVA_1_17 = 755;
    case JAVA_1_16_5 = 754;
    case JAVA_1_16_3 = 753;
    case JAVA_1_16_2 = 751;
    case JAVA_1_16_1 = 736;
    case JAVA_1_16 = 735;
    case JAVA_1_15_2 = 578;
    case JAVA_1_15_1 = 575;
    case JAVA_1_15 = 573;
    case JAVA_1_14_4 = 498;
    case JAVA_1_14_3 = 490;
    case JAVA_1_14_2 = 485;
    case JAVA_1_14_1 = 480;
    case JAVA_1_14 = 477;
    case JAVA_1_13_2 = 404;
    case JAVA_1_13_1 = 401;
    case JAVA_1_13 = 393;
    case JAVA_1_12_2 = 340;
    case JAVA_1_12_1 = 338;
    case JAVA_1_12 = 335;
    case JAVA_1_7_2 = 4;
}
