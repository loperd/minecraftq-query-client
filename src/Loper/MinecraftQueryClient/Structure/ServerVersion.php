<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Structure;

enum ServerVersion: string
{
    case VER_1_18_2 = '1.18.2';
    case VER_1_18_1 = '1.18.1';
    case VER_1_18 = '1.18';
    case VER_1_19 = '1.19';
    case VER_1_17_1 = '1.17.1';
    case VER_1_17 = '1.17';
    case VER_1_16_5 = '1.16.5';
    case VER_1_16_4 = '1.16.4';
    case VER_1_16_3 = '1.16.3';
    case VER_1_16_2 = '1.16.2';
    case VER_1_16_1 = '1.16.1';
    case VER_1_16 = '1.16';
    case VER_1_15_2 = '1.15.2';
    case VER_1_15_1 = '1.15.1';
    case VER_1_15 = '1.15';
    case VER_1_14_4 = '1.14.4';
    case VER_1_14_3 = '1.14.3';
    case VER_1_14_2 = '1.14.2';
    case VER_1_14_1 = '1.14.1';
    case VER_1_14 = '1.14';
    case VER_1_13_2 = '1.13.2';
    case VER_1_13_1 = '1.13.1';
    case VER_1_13 = '1.13';
    case VER_1_12_2 = '1.12.2';
    case VER_1_12_1 = '1.12.1';
    case VER_1_12 = '1.12';
    case VER_1_7_5 = '1.7.5';
    case VER_1_7_4 = '1.7.4';
    case VER_1_7_2 = '1.7.2';
}
