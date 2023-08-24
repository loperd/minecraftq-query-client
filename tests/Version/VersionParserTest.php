<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Version;

use Loper\Minecraft\Protocol\Struct\JavaServerVersion;
use Loper\MinecraftQueryClient\Exception\InvalidServerVersionException;
use Loper\MinecraftQueryClient\Java\JavaVersionParser;
use PHPUnit\Framework\TestCase;

final class VersionParserTest extends TestCase
{
    /**
     * @dataProvider versionsKitProvider
     * @dataProvider versionsProvider
     */
    public function test_correct_parsing_botfilter_version(string $inputVersion, JavaServerVersion $serverVersion): void
    {
        $version = JavaVersionParser::parse($inputVersion);

        self::assertEquals($serverVersion, $version);
    }

    /**
     * @dataProvider versionsProvider
     */
    public function test_correct_get_server_version(string $inputVersion, JavaServerVersion $serverVersion): void
    {
        $version = JavaVersionParser::getServerVersion($inputVersion);

        self::assertEquals($serverVersion, $version);
    }

    public static function versionsKitProvider(): array
    {
        return [
            ['1.8.x, 1.9.x, 1.10.x, 1.11.x, 1.12.x, 1.13.x, 1.14.x, 1.15.x, 1.16.x, 1.17.x, 1.18.x, 1.19.x', JavaServerVersion::JAVA_1_19_4]
        ];
    }

    public static function versionsProvider(): array
    {
        return [
            ['1.7.x', JavaServerVersion::JAVA_1_7_10],
            ['1.12.x', JavaServerVersion::JAVA_1_12_2],
            ['1.13.x', JavaServerVersion::JAVA_1_13_2],
            ['1.14.x', JavaServerVersion::JAVA_1_14_4],
            ['1.15.x', JavaServerVersion::JAVA_1_15_2],
            ['1.16.x', JavaServerVersion::JAVA_1_16_5],
            ['1.17.x', JavaServerVersion::JAVA_1_17_1],
            ['1.18.x', JavaServerVersion::JAVA_1_18_2],
            ['1.19.x', JavaServerVersion::JAVA_1_19_4],
            [JavaServerVersion::JAVA_1_7_2->value, JavaServerVersion::JAVA_1_7_2],
            [JavaServerVersion::JAVA_1_12_2->value, JavaServerVersion::JAVA_1_12_2],
            [JavaServerVersion::JAVA_1_13_1->value, JavaServerVersion::JAVA_1_13_1],
            [JavaServerVersion::JAVA_1_14_1->value, JavaServerVersion::JAVA_1_14_1],
            [JavaServerVersion::JAVA_1_15_1->value, JavaServerVersion::JAVA_1_15_1],
            [JavaServerVersion::JAVA_1_16_3->value, JavaServerVersion::JAVA_1_16_3],
            [JavaServerVersion::JAVA_1_17_1->value, JavaServerVersion::JAVA_1_17_1],
            [JavaServerVersion::JAVA_1_18_1->value, JavaServerVersion::JAVA_1_18_1],
            [JavaServerVersion::JAVA_1_19_4->value, JavaServerVersion::JAVA_1_19_4]
        ];
    }

    /**
     * @dataProvider incorrectVersions
     */
    public function test_incorrect_get_server_version(string $incorrectVersion): void
    {
        $this->expectException(InvalidServerVersionException::class);
        $this->expectExceptionMessage(\sprintf('Unable to parse the server version "%s".', $incorrectVersion));

        JavaVersionParser::getServerVersion($incorrectVersion);
    }

    public static function incorrectVersions(): array
    {
        return [
            [''],
            ['1.17-alfaromeo'],
            ['romka-chirka'],
        ];
    }

    public function test_correct_parsing_standart_two_digits_version(): void
    {
        $version = JavaVersionParser::parse(JavaServerVersion::JAVA_1_19->value);

        self::assertEquals(JavaServerVersion::JAVA_1_19, $version);
    }

    public function test_correct_parsing_standart_three_digits_version(): void
    {
        $version = JavaVersionParser::parse(JavaServerVersion::JAVA_1_18_2->value);

        self::assertEquals(JavaServerVersion::JAVA_1_18_2, $version);
    }

    public function test_correct_parsing_botfilter_three_digits_version(): void
    {
        $version = JavaVersionParser::parse('1.18.x');

        self::assertEquals(JavaServerVersion::JAVA_1_18_2, $version);
    }

    public function test_correct_parsing_botfilter_three_invalid_digits_version(): void
    {
        $version = JavaVersionParser::parse('1.19.x');

        self::assertEquals(JavaServerVersion::JAVA_1_19_4, $version);
    }

    public function test_empty_string_data(): void
    {
        $this->expectException(InvalidServerVersionException::class);
        $this->expectExceptionMessage('Version cannot be empty.');

        JavaVersionParser::parse('');
    }


    /**
     * @dataProvider incorrectVersionProvider
     */
    public function test_incorrect_string_data(string $version): void
    {
        $this->expectException(InvalidServerVersionException::class);
        $this->expectExceptionMessage(\sprintf('Unable to parse the server version "%s".', $version));

        JavaVersionParser::parse($version);
    }

    public static function incorrectVersionProvider(): array
    {
        return [
            ['abc'],
            ['1-12.x'],
            ['1.x.12'],
            ['x1.1'],
        ];
    }


    /**
     * @dataProvider versionsForProcessProvider
     */
    public function test_process_version(string $inputVersion, string $resultVersion): void
    {
        $version = JavaVersionParser::processVersion($inputVersion);

        self::assertEquals($resultVersion, $version);
    }

    public static function versionsForProcessProvider(): array
    {
        return [
            ['1.12.x', JavaServerVersion::JAVA_1_12->value],
            ['1.13.x', JavaServerVersion::JAVA_1_13->value],
            ['1.14.x', JavaServerVersion::JAVA_1_14->value],
            ['1.15.x', JavaServerVersion::JAVA_1_15->value],
            ['1.16.x', JavaServerVersion::JAVA_1_16->value],
            ['1.17.x', JavaServerVersion::JAVA_1_17->value],
            ['1.18.x', JavaServerVersion::JAVA_1_18->value],
            ['1.19.x', JavaServerVersion::JAVA_1_19->value],
            ['1.7.5', JavaServerVersion::JAVA_1_7_5->value],
            ['1.12.2', JavaServerVersion::JAVA_1_12_2->value],
            ['1.13.2', JavaServerVersion::JAVA_1_13_2->value],
            ['1.14.4', JavaServerVersion::JAVA_1_14_4->value],
            ['1.15.2', JavaServerVersion::JAVA_1_15_2->value],
            ['1.16.5', JavaServerVersion::JAVA_1_16_5->value],
            ['1.17.1', JavaServerVersion::JAVA_1_17_1->value],
            ['1.18.2', JavaServerVersion::JAVA_1_18_2->value],
            ['1.19', JavaServerVersion::JAVA_1_19->value],
        ];
    }
}
