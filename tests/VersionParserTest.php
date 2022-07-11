<?php

declare(strict_types=1);

use Loper\MinecraftQueryClient\Service\VersionParser;
use Loper\MinecraftQueryClient\Structure\ServerVersion;
use PHPUnit\Framework\TestCase;

final class VersionParserTest extends TestCase
{
    public function test_correct_parsing_botfilter_version()
    {
        $input = '1.8.x, 1.9.x, 1.10.x, 1.11.x, 1.12.x, 1.13.x, 1.14.x, 1.15.x, 1.16.x, 1.17.x, 1.18.x';

        $version = VersionParser::parse($input);

        self::assertEquals(ServerVersion::JAVA_1_18_2, $version);
    }

    public function test_correct_parsing_standart_two_digits_version()
    {
        $version = VersionParser::parse(ServerVersion::JAVA_1_19->value);

        self::assertEquals(ServerVersion::JAVA_1_19, $version);
    }

    public function test_correct_parsing_standart_three_digits_version()
    {
        $version = VersionParser::parse(ServerVersion::JAVA_1_18_2->value);

        self::assertEquals(ServerVersion::JAVA_1_18_2, $version);
    }

    public function test_correct_parsing_botfilter_three_digits_version()
    {
        $version = VersionParser::parse('1.18.x');

        self::assertEquals(ServerVersion::JAVA_1_18_2, $version);
    }

    public function test_correct_parsing_botfilter_three_invalid_digits_version()
    {
        $version = VersionParser::parse('1.19.x');

        self::assertEquals(ServerVersion::JAVA_1_19, $version);
    }

    public function test_empty_string_data()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Version cannot be empty.');

        VersionParser::parse('');
    }
}
