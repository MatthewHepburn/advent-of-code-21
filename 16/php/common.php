<?php

namespace AdventOfCode\TwentyTwo\PacketDecoder;

use Exception;
use Logger;

class Transmission
{
    /** @var array string[] */
    private array $annotation = [];
    /** @var array int[] */
    private array $originalBinary;

    /**
     * @param int[] $binary
     */
    public function __construct(private array $binary, private Logger $logger)
    {
        $this->originalBinary = $this->binary;
    }

    public function readPacket(): Packet
    {
        $version = $this->takeBitsAsInt(3, 'V');
        $typeId = $this->takeBitsAsInt(3, 'T');

        if ($typeId === 4) {
            return $this->readLiteralValuePacket($version);
        }

        return $this->readOperatorPacket($typeId, $version);

        throw new Exception("Unknown Packet type $typeId");
    }

    private function readLiteralValuePacket(int $version): LiteralValue
    {
        $this->logger->log("reading literal packet by length (version = $version)");
        $type = ord('A');
        $binary = [];
        $packetsRemaining = true;
        while ($packetsRemaining) {
            $typeChar = chr($type);

            $packetsRemaining = 1 === $this->takeBitsAsBinary(1, $typeChar)[0];

            $signalBits = $this->takeBitsAsBinary(4, $typeChar);
            $binary = array_merge($binary,  $signalBits);
            $type++;
        }

        $value = bin2int($binary);
        return new LiteralValue($version, $value);
    }

    private function readOperatorPacket(int $typeId, int $version): Operator
    {
        $lengthTypeIdArray = $this->takeBitsAsBinary(1, 'I');
        if (count($lengthTypeIdArray) !== 1) {
            throw new Exception('Count not read length type ID');
        }
        if ($lengthTypeIdArray[0] === 0) {
            $this->logger->log("reading operator packet by length (typeId = $typeId, version = $version)");
            return $this->readOperatorPacketByLength($typeId, $version);
        } else {
            $this->logger->log("reading operator packet by packets (typeId = $typeId, version = $version)");
            return $this->readOperatorPacketByPackets($typeId, $version);
        }
    }

    public function peek(): ?int
    {
        return $this->binary[0] ?? null;
    }

    public function takeBitsAsBinary(int $bits, string $bitType): array
    {
        $output = [];
        for ($i = 0; $i < $bits && count($this->binary) > 0; $i++) {
            $output[]= array_shift($this->binary);
            $this->annotation[]= $bitType;
        }

        return $output;
    }

    public function takeBitsAsInt(int $bits, string $bitType): int
    {
        $bits = $this->takeBitsAsBinary($bits, $bitType);
        return bin2int($bits);
    }

    private static function bin2str(array $binary): string
    {
        return implode('', array_map(fn(int $x) => (string) $x, $binary));
    }

    public function __toString(): string
    {
        $output =  'Remaining  => ' . self::bin2str($this->binary) . "\n";
        $output .= 'Original   => ' . self::bin2str($this->originalBinary) . "\n";
        $output .= 'Annotation => ' . implode('', $this->annotation);

        return $output;
    }

    public static function fromHex(string $hexString, Logger $logger) : static
    {
        $binary = [];
        foreach (str_split($hexString) as $hexFigure) {
            $int = hexdec($hexFigure);
            foreach ([8, 4, 2, 1] as $pow) {
                if ($int >= $pow) {
                    $binary[]= 1;
                    $int -= $pow;
                } else {
                    $binary[]= 0;
                }
            }
        }

        return new static($binary, $logger);
    }

    private function readOperatorPacketByLength(int $typeId, int $version): Operator
    {
        $subPacketsLength = bin2int($this->takeBitsAsBinary(15, 'L'));
        $startPos = count($this->binary);
        $currentPos = $startPos;

        $subPackets = [];
        while ($currentPos > ($startPos - $subPacketsLength)) {
            $subPackets[]= $this->readPacket();
            $currentPos = count($this->binary);
        }

        return new Operator($typeId, $version, $subPackets);
    }

    private function readOperatorPacketByPackets(int $typeId, int $version): Operator
    {
        $subPacketCount = bin2int($this->takeBitsAsBinary(11, 'L'));
        $subPackets = [];
        for ($i = 0; $i < $subPacketCount; $i++) {
            $subPackets[]= $this->readPacket();
        }

        return new Operator($typeId, $version, $subPackets);
    }
}

interface Packet
{
    public function getTypeId(): int;
    public function getVersion(): int;
    public function getVersionSum(): int;
    public function getValue(): int;

    /**
     * @return Packet[]
     */
    public function getPackets(): array;
    public function __toString(): string;
}

class LiteralValue implements Packet
{
    public function __construct(private readonly int $version, public readonly int $value)
    {
    }

    public function getTypeId(): int
    {
        return 4;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getVersionSum(): int
    {
        return $this->getVersion();
    }

    /**
     * @return Packet[]
     */
    public function getPackets(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return "[Literal - value = $this->value, version = $this->version]";
    }

    public function getValue(): int
    {
        return $this->value;
    }
}

class Operator implements Packet
{
    public function __construct(private readonly int $typeId, private readonly int $version, private array $packets)
    {
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return Packet[]
     */
    public function getPackets(): array
    {
        return $this->packets;
    }

    public function getVersionSum(): int
    {
        return $this->getVersion() + array_sum(array_map(fn(Packet $p) => $p->getVersionSum(), $this->getPackets()));
    }

    public function getValue(): int
    {
        $subValues = array_map(fn(Packet $p) => $p->getValue(), $this->getPackets());
        return match ($this->typeId) {
            0 => array_sum($subValues),
            1 => array_product($subValues),
            2 => min($subValues),
            3 => max($subValues),
            5 => $subValues[0] > $subValues[1] ? 1 : 0,
            6 => $subValues[0] < $subValues[1] ? 1 : 0,
            7 => $subValues[0] == $subValues[1] ? 1 : 0,
        };
    }

    public function __toString(): string
    {
        return "[Operator - typeId = $this->typeId, version = $this->version]";
    }
}

function bin2int(array $bits): int
{
    $output = 0;
    $pow = 1;
    foreach (array_reverse($bits) as $bit) {
        $output += $pow * $bit;
        $pow = $pow * 2;
    }

    return $output;
}
