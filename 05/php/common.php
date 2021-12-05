<?php

class Point
{
    public function __construct(private int $x, private int $y) {}

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function __toString(): string
    {
        return "({$this->x}, {$this->y})";
    }

    public static function fromInput(string $input): Point
    {
        $parts = explode(',', $input);
        if (count($parts) !== 2) {
            throw new Exception("Failed to parse Point from input '$input'");
        }

        return new Point((int) $parts[0], (int) $parts[1]);
    }
}

class Line
{
    public function __construct(private Point $start, private Point $end)
    {
        // Normalise points, so they always go from -x to +x
        if ($this->start->getX() > $this->end->getX()) {
            $this->start = $end;
            $this->end = $start;
        }
    }

    /**
     * @return Point
     */
    public function getStart(): Point
    {
        return $this->start;
    }

    /**
     * @return Point
     */
    public function getEnd(): Point
    {
        return $this->end;
    }

    public function isHorizontal(): bool
    {
        return $this->start->getY() === $this->end->getY();
    }

    public function isVertical(): bool
    {
        return $this->start->getX() === $this->end->getX();
    }

    public function __toString(): string
    {
        return "{$this->start} -> {$this->end}";
    }

    public static function fromInput(string $input): Line
    {
        $parts = explode(' -> ', $input);
        if (count($parts) !== 2) {
            throw new Exception("Failed to split line into points for input '$input'");
        }

        return new Line(Point::fromInput($parts[0]), Point::fromInput($parts[1]));
    }
}

/**
 * @param Line $line
 *
 * @return Point[]
 */
function getPointsOnHorizontalLine(Line $line): array
{
    $output = [];

    $y = $line->getStart()->getY();
    foreach (range($line->getStart()->getX(), $line->getEnd()->getX()) as $x) {
        $output[]= new Point($x, $y);
    }

    return $output;
}

/**
 * @param Line $line
 *
 * @return Point[]
 */
function getPointsOnVerticalLine(Line $line): array
{
    $output = [];

    $x = $line->getStart()->getX();
    foreach (range($line->getStart()->getY(), $line->getEnd()->getY()) as $y) {
        $output[]= new Point($x, $y);
    }

    return $output;
}

/**
 * @param Line $line
 *
 * @return Point[]
 */
function getPointsOnDiagonalLine(Line $line): array
{
    $output = [];
    $yDirection = $line->getEnd()->getY() > $line->getStart()->getY() ? 1 : -1;

    $y = $line->getStart()->getY();
    foreach (range($line->getStart()->getX(), $line->getEnd()->getX()) as $x) {
        $output[]= new Point($x, $y);
        $y += $yDirection;
    }

    return $output;
}
