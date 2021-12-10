<?php

class Map
{
    private int $maxX;
    private int $maxY;

    public function __construct(private array $grid)
    {
        $this->maxX = count($this->grid) - 1;
        $this->maxY = count($this->grid[0]) - 1;
    }

    /**
     * @return Point[]
     */
    public function getAllPoints(): array
    {
        $points = [];
        for ($x = 0; $x <= $this->maxX; $x++) {
            for ($y = 0; $y <= $this->maxY; $y++) {
                $points[]= new Point($x, $y, $this->grid[$x][$y]);
            }
        }

        return $points;
    }

    /**
     * @param Point $point
     *
     * @return Point[]
     */
    public function getNeighbours(Point $point): array
    {
        $neighbours = [
            $this->getPointAt($point->x - 1, $point->y),
            $this->getPointAt($point->x + 1, $point->y),
            $this->getPointAt($point->x, $point->y - 1),
            $this->getPointAt($point->x, $point->y + 1),
        ];

        return filter($neighbours);
    }

    public function getPointAt(int $x, int $y): ?Point
    {
        if ($x < 0 || $x > $this->maxX) {
            return null;
        }
        if ($y < 0 || $y > $this->maxY) {
            return null;
        }

        return new Point($x, $y, $this->grid[$x][$y]);
    }

    /**
     * @return Point[]
     */
    public function getMinima(): array
    {
        $minima = [];
        foreach ($this->getAllPoints() as $point) {
            $neighbours = $this->getNeighbours($point);
            foreach ($neighbours as $neighbour) {
                if ($neighbour->value <= $point->value) {
                    continue 2;
                }
            }
            $minima[]= $point;
        }

        return $minima;
    }

    /**
     * @param Point $point
     *
     * @return Point[]
     */
    public function getNeighbouringBasinPointsAbove(Point $point): array
    {
        $neighbours = $this->getNeighbours($point);
        $basinNeighbours = filter($neighbours, fn(Point $x) => $x->value < 9);
        return filter($basinNeighbours, fn(Point $x) => $x->value > $point->value);
    }

    public static function fromInput(array $lines): Map
    {
        $grid = [];
        foreach ($lines as $line) {
            $chars = str_split($line);
            $grid[]= array_map(fn(string $x) => (int) $x, $chars);
        }

        return new Map($grid);
    }
}

class Point
{
    public function __construct(public int $x, public int $y, public int $value)
    {
    }

    public function __toString(): string
    {
        return "($this->x, $this->y)[$this->value]";
    }
}
