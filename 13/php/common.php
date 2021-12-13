<?php

class FoldablePoint
{
    public function __construct(public int $x, public int $y)
    {
    }

    public function __toString(): string
    {
        return "($this->x, $this->y)";
    }

    public function fold(FoldInstruction $foldInstruction): void
    {
        $axis = $foldInstruction->axis;
        $myValue = $this->$axis;
        if ($myValue < $foldInstruction->value) {
            // Above or left of the fold, no action to take
            return;
        }

        $distance = $myValue - $foldInstruction->value;
        $this->$axis = $foldInstruction->value - $distance;
    }

    public static function fromInputLine(string $line): static
    {
        $parts = explode(',', $line);
        return new static((int) $parts[0], (int) $parts[1]);
    }
}

class FoldInstruction
{
    public function __construct(public string $axis, public int $value)
    {
    }

    public function __toString(): string
    {
        return "fold along {$this->axis}={$this->value}";
    }

    public static function fromInputLine(string $line): static
    {
        $equation = explode(' ', $line)[2];
        $sides = explode('=', $equation);
        return new static($sides[0], (int) $sides[1]);
    }
}
