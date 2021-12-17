<?php

namespace AdventOfCode\TwentyOne\Chiton;

use Common\Point;
use Exception;

require_once __DIR__ . '/../../common/php/Point.php';

class Map
{
    private int $maxX;
    private int $maxY;
    private array $frontier;
    private array $costMap;

    /**
     * @param int[][] $riskMap
     */
    public function __construct(private array $riskMap)
    {
        $this->maxX = count($this->riskMap) - 1;
        $this->maxY = count($this->riskMap[0]) - 1;
        $this->frontier = [ new Point(0,0) ];

        $this->costMap = [];
        for ($x = 0; $x <= $this->maxX; $x++) {
            $ys = [];
            for ($y = 0; $y <= $this->maxY; $y++) {
                $ys[]= null;
            }
            $this->costMap[]= $ys;
        }

        $this->costMap[0][0] = 0;
    }

    public function step(): void
    {
        $newFrontier = [];
        foreach ($this->frontier as $point) {
            $startRiskCost = $this->getTravelRiskCost($point);
            foreach ($this->getMovesFrom($point) as $move) {
                $endX = $move->end->getX();
                $endY = $move->end->getY();
                $endRiskCost = $startRiskCost + $move->cost;
                if (!isset($this->costMap[$endX][$endY]) || $this->costMap[$endX][$endY] > $endRiskCost) {
                    // We've found a new or better way to reach this point. Record it and come back to it
                    $this->costMap[$endX][$endY] = $endRiskCost;
                    // Use string representation to deduplicate points
                    $newFrontier[(string) $move->end] = $move->end;
                }
            }
        }

        $this->frontier = $newFrontier;
    }

    public function hasFrontier(): bool
    {
        return count($this->frontier) > 0;
    }

    public function getEnd(): Point
    {
        return new Point($this->maxX, $this->maxY);
    }

    public function getRiskAt(Point $point): ?int
    {
        if ($point->getX() < 0 || $this->maxX < $point->getX()) {
            return null;
        }

        if ($point->getY() < 0 || $this->maxY < $point->getY()) {
            return null;
        }

        return $this->riskMap[$point->getX()][$point->getY()];
    }

    public function getTravelRiskCost(Point $point): ?int
    {
        if ($point->getX() < 0 || $this->maxX < $point->getX()) {
            throw new Exception("Point out of bounds $point");
        }

        if ($point->getY() < 0 || $this->maxY < $point->getY()) {
            throw new Exception("Point out of bounds $point");
        }

        return $this->costMap[$point->getX()][$point->getY()];
    }

    /**
     * @return Move[]
     */
    public function getMovesFrom(Point $p): array
    {
        $x = $p->getX();
        $y = $p->getY();
        $possibleDestinations = [
            'up' => new Point($x, $y - 1),
            'down' => new Point($x, $y + 1),
            'left' => new Point($x - 1, $y),
            'right' => new Point($x + 1, $y)
        ];

        $possibleCosts = array_map(fn(Point $p) => $this->getRiskAt($p), $possibleDestinations);
        $possibleCosts = array_filter($possibleCosts,  fn(?int $x) => $x !== null);

        $possibleMoves = [];
        foreach ($possibleCosts as $direction => $cost) {
            $possibleMoves[$direction] = new Move($possibleDestinations[$direction], $cost);
        }

        return $possibleMoves;
    }

    public function getCostMap(): string
    {
        $output = '';
        foreach ($this->costMap as $line) {
            $output .= implode("\t", $line) . "\n";
        }

        return $output;
    }
}


class Move
{
    public function __construct(public Point $end, public int $cost) {

    }
}
