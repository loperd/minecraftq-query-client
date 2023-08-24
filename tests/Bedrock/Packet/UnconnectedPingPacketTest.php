<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Bedrock\Packet;

use Loper\Minecraft\Protocol\Struct\BedrockProtocolVersion;
use Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket;
use Loper\MinecraftQueryClient\Exception\PacketReadException;
use Loper\MinecraftQueryClient\Stream\ByteBufferInputStream;
use Loper\MinecraftQueryClient\Stream\ByteBufferOutputStream;
use PHPinnacle\Buffer\ByteBuffer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UnconnectedPingPacketTest extends TestCase
{
    #[DataProvider('successReadPacketDataProvider')]
    public function test_success_read(string $bytes, array $data): void
    {
        $buffer = new ByteBuffer(base64_decode($bytes, true));
        $is = new ByteBufferInputStream($buffer);

        $packet = new UnconnectedPingPacket();
        $packet->read($is, BedrockProtocolVersion::BEDROCK_1_20_12);

        self::assertEquals($data['pingId'], $packet->pingId);
        self::assertEquals($data['serverId'], $packet->serverId);
        self::assertEquals($data['gameId'], $packet->gameId);
        self::assertEquals($data['rawDescription'], $packet->rawDescription);
        self::assertEquals($data['description'], $packet->description);
        self::assertEquals($data['protocol'], $packet->protocol->value);
        self::assertEquals($data['currentPlayers'], $packet->currentPlayers);
        self::assertEquals($data['name'], $packet->name);
        self::assertEquals($data['maxPlayers'], $packet->maxPlayers);
        self::assertEquals($data['gameVersion'], $packet->gameVersion);
        self::assertEquals($data['mode'], $packet->mode);
    }

    #[DataProvider('failReadPacketDataProvider')]
    public function test_incorrect_packet_id(string $bytes, string $expectedException, ?string $expectedExceptionMessage = null): void
    {
        $buffer = new ByteBuffer(base64_decode($bytes, true));
        $is = new ByteBufferInputStream($buffer);

        $this->expectException($expectedException);
        if (null !== $expectedExceptionMessage) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $packet = new UnconnectedPingPacket();
        $packet->read($is, BedrockProtocolVersion::BEDROCK_1_20_12);
    }

    public function test_success_write(): void
    {
        $packet = new UnconnectedPingPacket();
        $os = new ByteBufferOutputStream(new ByteBuffer());

        $packet->write($os, BedrockProtocolVersion::BEDROCK_1_20_12);

        self::assertEquals($this->createExpectedWriteByteBuffer()->bytes(), $os->getBuffer()->bytes());
    }

    public static function failReadPacketDataProvider(): \Generator
    {
        yield [
            'GwAAAABk4flraRfj24e7GGAA//8A/v7+/v39/f0SNFZ4AF9NQ1BFO1Bvd2VyTnVra2l0IFNlcnZlcjs0NzE7MS4xNy40MDswOzIwOzc1NzI3NzE4MzA0NjEzMDY5NzY7aHR0cHM6Ly9wb3dlcm51a2tpdC5vcmc7U3Vydml2YWw7MQ==',
            PacketReadException::class,
            'Failed to read packet: "Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket". Detail: "packet id is not UNCONNECTED_PONG"'
        ];
        yield [
            'HAAAAABk4fqzULdobBIFVQ5mPWymHqKpkx/LXCn6lQMNAGdNQ1BFO8KnbMKnZsKna2lpwqdyIMKnbMKnY0Jsb29kwqc2TWluZSDCp3LCp2zCp2bCp2tpacKnciDCp2Z2MS4xOzEwMTsxLjE2OzI1NDsxNjAwOzc0NDg4MTU1NDk3MTQ4NDMzMDE7',
            PacketReadException::class,
            'Failed to read packet: "Loper\MinecraftQueryClient\Bedrock\Packet\UnconnectedPingPacket". Detail: "magic bytes is difference"'
        ];
    }

    public static function successReadPacketDataProvider(): \Generator
    {
        yield [
            'HAAAAABk4flraRfj24e7GGAA//8A/v7+/v39/f0SNFZ4AF9NQ1BFO1Bvd2VyTnVra2l0IFNlcnZlcjs0NzE7MS4xNy40MDswOzIwOzc1NzI3NzE4MzA0NjEzMDY5NzY7aHR0cHM6Ly9wb3dlcm51a2tpdC5vcmc7U3Vydml2YWw7MQ==',
            [
                "pingId" => 1692531051,
                "serverId" => 7572771830461306976,
                "protocol" => BedrockProtocolVersion::BEDROCK_1_17_41->value,
                "currentPlayers" => 0,
                "maxPlayers" => 20,
                "rawDescription" => "PowerNukkit Server",
                "description" => "PowerNukkit Server",
                "name" => "https://powernukkit.org",
                "mode" => "Survival",
                "gameId" => 'MCPE',
                "gameVersion" => '1.17.40',
            ]
        ];

        yield [
            'HAAAAABk4fqzULdobBIFVQ4A//8A/v7+/v39/f0SNFZ4AGdNQ1BFO8KnbMKnZsKna2lpwqdyIMKnbMKnY0Jsb29kwqc2TWluZSDCp3LCp2zCp2bCp2tpacKnciDCp2Z2MS4xOzEwMTsxLjE2OzI1NDsxNjAwOzc0NDg4MTU1NDk3MTQ4NDMzMDE7',
            [
                "pingId" => 1692531379,
                "serverId" => 5816232257140380942,
                "rawDescription" => "§l§f§kii§r §l§cBlood§6Mine §r§l§f§kii§r §fv1.1",
                "description" => "ii BloodMine ii v1.1",
                "protocol" => BedrockProtocolVersion::POCKET_ALPHA_1_0_4_0->value,
                "currentPlayers" => 254,
                "maxPlayers" => 1600,
                "name" => '',
                "mode" => null,
                "gameId" => 'MCPE',
                "gameVersion" => '1.16',
            ]
        ];
    }

    private function createExpectedWriteByteBuffer(): ByteBuffer
    {
        $expectedBb = new ByteBuffer();
        foreach (UnconnectedPingPacket::OFFLINE_MESSAGE_DATA_ID as $byte) {
            $expectedBb->appendInt8($byte);
        }
        $expectedBb->appendUint64(2);

        return $expectedBb;
    }
}
