<?php

class SevenSegmentDisplay
{
    private string $illuminatedSegments;

    public function __construct(private int $number)
    {
        $this->illuminatedSegments = $this->getSegmentsForNumber($number);
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getIlluminatedSegments(): array
    {
        return str_split($this->illuminatedSegments);
    }

    public function __toString(): string
    {
        foreach (['a', 'b', 'c', 'd', 'e', 'f', 'g'] as $position) {
            // Can't resist a variable variable
            $$position = str_contains($this->illuminatedSegments, $position) ? $position : ' ';
        }

        return
<<<STR
  $a$a$a$a 
 $b    $c
 $b    $c
  $d$d$d$d
 $e    $f
 $e    $f
  $g$g$g$g
STR; // PHP can be so beautiful sometimes
    }

    private function getSegmentsForNumber(int $number): string
    {
        switch ($number) {
            case 0:
                return 'abcefg';
            case 1:
                return 'cf';
            case 2:
                return 'acdeg';
            case 3:
                return 'acdfg';
            case 4:
                return 'bcdf';
            case 5:
                return 'abdfg';
            case 6:
                return 'abdefg';
            case 7:
                return 'acf';
            case 8:
                return 'abcdefg';
            case 9:
                return 'abcdfg';
            default:
                throw new Exception("Cannot show $number on a seven segment display!");
        }
    }
}

class Observation
{
    public function __construct(private array $signals, private array $outputs) {}

    public function getSignals(): array
    {
        return $this->signals;
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public static function fromInputLine(string $input): Observation
    {
        $halves = explode('|', $input);
        if (count($halves) !== 2) {
            throw new Exception("Failed to split line on |: '$input'");
        }

        $signalStrings = explode(' ', trim($halves[0]));
        $signalArrays = array_map(fn ($x) => str_split($x), $signalStrings);
        array_map(fn(array $a) => sort($a), $signalArrays);

        $outputStrings = explode(' ', trim($halves[1]));
        $outputArrays = array_map(fn ($x) => str_split($x), $outputStrings);
        array_map(fn(array $a) => sort($a), $outputArrays);

        return new Observation($signalArrays, $outputArrays);
    }
}

class WireMapping
{
    // string -> digit
    private array $wiresToDigit = [];
    private array $wireToSegmentPossibilities;
    private array $wireToSegments;

    public function __construct()
    {
        // Initially, any wire could map to any segment
        $this->wireToSegmentPossibilities = [];
        $letters = (new SevenSegmentDisplay(8))->getIlluminatedSegments();
        foreach ($letters as $letter) {
            $this->wireToSegmentPossibilities[$letter] = $letters;
        }
    }

    public function getDigitFromOutput(array $output): int {
        $segments = [];
        foreach ($output as $outputSegment) {
            $segments[]= $this->wireToSegments[$outputSegment];
        }
        sort($segments);

        for ($i = 0; $i < 10; $i++) {
            if ((new SevenSegmentDisplay($i))->getIlluminatedSegments() == $segments) {
                return $i;
            }
        }

        throw new Exception('Could not find digit for output:' . json_encode($segments));
    }

    public function setWiresForDigit(array $wires, int $digit): void
    {
        $key = implode('', $wires);
        $this->wiresToDigit[$key] = $digit;

        $segmentsForDigit = (new SevenSegmentDisplay($digit))->getIlluminatedSegments();

        // We can remove any illuminated segments as possibilities for the other wires
        $otherWires = $this->getInverse($wires);
        foreach ($otherWires as $otherWire) {
            $this->wireToSegmentPossibilities[$otherWire] = array_filter($this->wireToSegmentPossibilities[$otherWire], function ($possibility) use ($segmentsForDigit) {
               return !in_array($possibility, $segmentsForDigit, true);
            });
        }

        // We can also remove any unlit segments as possibilities for our input wires
        foreach ($wires as $wire) {
            $this->wireToSegmentPossibilities[$wire] = array_filter($this->wireToSegmentPossibilities[$wire], function ($possibility) use ($segmentsForDigit) {
                return in_array($possibility, $segmentsForDigit, true);
            });
        }
    }

    public function removeInvalidFromPossible(array $wires, array $possibleDigits): void
    {
        if (count($possibleDigits) === 0) {
            throw new Exception('Empty digit list!');
        }

        $possiblyIlluminatedSegments = [];
        foreach ($possibleDigits as $possibleDigit) {
            $segmentsForDigit = (new SevenSegmentDisplay($possibleDigit))->getIlluminatedSegments();
            $possiblyIlluminatedSegments = array_merge($possiblyIlluminatedSegments, $segmentsForDigit);
        }

        $impossibleSegments = $this->getInverse($possiblyIlluminatedSegments);

        // For each of our wires, remove any impossible segments
        foreach ($wires as $wire) {
            $this->wireToSegmentPossibilities[$wire] = array_filter($this->wireToSegmentPossibilities[$wire], function ($possibility) use ($impossibleSegments) {
                return !in_array($possibility, $impossibleSegments, true);
            });
        }

        // Are there any segments that MUST be illuminated? I.e. Would be lit for all possible digits
        $mustBeIlluminated = $this->getInverse([]);
        foreach ($possibleDigits as $possibleDigit) {
            $segmentsForDigit = (new SevenSegmentDisplay($possibleDigit))->getIlluminatedSegments();
            $mustBeIlluminated = array_filter($mustBeIlluminated, function (string $letter) use ($segmentsForDigit) {
                return in_array($letter, $segmentsForDigit);
            });
        }

        // Any segments that must be illuminated can be eliminated for the other wires
        $otherWires = $this->getInverse($wires);
        foreach ($otherWires as $otherWire) {
            $this->wireToSegmentPossibilities[$otherWire] = array_filter($this->wireToSegmentPossibilities[$otherWire], function ($possibility) use ($mustBeIlluminated) {
                return !in_array($possibility, $mustBeIlluminated, true);
            });
        }
    }

    public function completeDeduction(): void
    {
        // Lock in all mappings with exactly one possibility
        $lockedInSegments = [];
        foreach ($this->wireToSegmentPossibilities as $wire => $segmentPossibilities) {
            $segmentPossibilities = array_values($segmentPossibilities);
            $this->wireToSegmentPossibilities[$wire] = $segmentPossibilities; // Normalise array structure after array_filter has been at it
            if (count($segmentPossibilities) === 1) {
                $lockedInSegments[]= $segmentPossibilities[0];
            }
        }

        foreach ($this->wireToSegmentPossibilities as $wire => $segmentPossibilities) {
            if (count($segmentPossibilities) !== 1) {
                $remainingPossibilities = array_values(array_diff($segmentPossibilities, $lockedInSegments));
                $this->wireToSegmentPossibilities[$wire] = $remainingPossibilities;
            }
        }

        // Check we've got a final mapping, convert to final form
        $this->wireToSegments = [];
        foreach ($this->wireToSegmentPossibilities as $wire => $segmentPossibilities) {
            if (count($segmentPossibilities) !== 1) {
                throw new Exception("No fixed mapping for wire '$wire'");
            }
            $this->wireToSegments[$wire] = $segmentPossibilities[0];
        }
    }


    private function getInverse(array $letters): array
    {
        $allLetters = (new SevenSegmentDisplay(8))->getIlluminatedSegments();
        $output = [];
        foreach ($allLetters as $letter) {
            if (!in_array($letter, $letters)) {
                $output[]= $letter;
            }
        }

        return $output;
    }

    public function __toString(): string
    {
        $output = [
            'wireToDigit' => $this->wiresToDigit,
            'wireToSegmentPossibilities' => $this->wireToSegmentPossibilities
        ];

        return json_encode($output, JSON_PRETTY_PRINT);
    }
}

function getSegmentCountMap(): array
{
    $map = [];
    for ($i = 0; $i < 10; $i++) {
        $segment = new SevenSegmentDisplay($i);
        $map[$i] = count($segment->getIlluminatedSegments());
    }

    return $map;
}
