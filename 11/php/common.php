<?php

class OctopusGrid
{
    private int $maxX;
    private int $maxY;
    /** @var Octopus[] Flat array of octos for ease of iteration */
    private array $allOctos = [];

    /**
     * @param Octopus[][] $octoGrid
     */
    public function __construct(private array $octoGrid)
    {
        $this->maxX = count($this->octoGrid) - 1;
        $this->maxY = count($this->octoGrid[0]) - 1;

        foreach ($this->octoGrid as $row) {
            foreach ($row as $octopus) {
                $this->allOctos[]= $octopus;
            }
        }
    }

    public function step(): int
    {
        // Step 1 - Energise all octos
        array_map(fn(Octopus $octopus) => $octopus->energise(), $this->allOctos);

        // Step 2 - Discharge octos until no fully charged octos remain
        $totalFlashes = 0;

        do {
            $flashesThisRound = 0;

            $fullyChargedOctos = filter($this->allOctos, fn(Octopus $octopus) => $octopus->getEnergyLevel() > 9 && !$octopus->hasFlashed());
            foreach ($fullyChargedOctos as $fullyChargedOctopus) {
                // Octopus flashes
                $fullyChargedOctopus->flash();
                $flashesThisRound++;
                $totalFlashes++;

                // Energise the neighbours
                $neighbours = $this->getNeighbours($fullyChargedOctopus);
                array_map(fn(Octopus $octopus) => $octopus->energise(), $neighbours);
            }
        } while ($flashesThisRound > 0);

        // Step 3 - Reset all over-charged octos to energy level 0
        $overchargedOctos = filter($this->allOctos, fn(Octopus $octopus) => $octopus->getEnergyLevel() > 9);
        array_map(fn(Octopus $octopus) => $octopus->resetEnergyLevel(), $overchargedOctos);

        return $totalFlashes;
    }

    /**
     * @param Octopus $octopus
     *
     * @return Octopus[]
     */
    public function getNeighbours(Octopus $octopus): array
    {
        $x = $octopus->getX();
        $y = $octopus->getY();

        $neighbours = [
            $this->getOctopusAt($x - 1, $y),
            $this->getOctopusAt($x + 1, $y),
            $this->getOctopusAt($x, $y - 1),
            $this->getOctopusAt($x, $y + 1),

            // Also include diagonals this time
            $this->getOctopusAt($x - 1, $y - 1),
            $this->getOctopusAt($x - 1, $y + 1),
            $this->getOctopusAt($x + 1, $y - 1),
            $this->getOctopusAt($x + 1, $y + 1)
        ];

        return filter($neighbours);
    }

    public function getOctopusAt(int $x, int $y): ?Octopus
    {
        if ($x < 0 || $x > $this->maxX) {
            return null;
        }
        if ($y < 0 || $y > $this->maxY) {
            return null;
        }

        return $this->octoGrid[$x][$y];
    }

    public function getOctoCount(): int
    {
        return count($this->allOctos);
    }

    public function __toString(): string
    {
        $output = '';
        foreach ($this->octoGrid as $row) {
            foreach ($row as $octopus) {
                $output .= $octopus->getEnergyLevel();
            }
            $output .= "\n";
        }

        return $output;
    }

    public static function fromInput(array $lines): static
    {
        $octos = [];
        foreach ($lines as $x => $line) {
            $chars = str_split($line);
            $octos[]= array_map(fn(string $char, int $y) => new Octopus($x, $y, (int) $char), $chars, array_keys($chars));
        }

        return new OctopusGrid($octos);
    }
}

class Octopus
{
    private bool $hasFlashed = false;

    public function __construct(private int $x, private int $y, private int $energyLevel)
    {
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function energise(): void
    {
        $this->energyLevel += 1;
    }

    public function getEnergyLevel(): int
    {
        return $this->energyLevel;
    }

    public function resetEnergyLevel(): void
    {
        $this->energyLevel = 0;
        $this->hasFlashed = false;
    }

    public function hasFlashed(): bool
    {
        return $this->hasFlashed;
    }

    public function flash(): void
    {
        $this->hasFlashed = true;
    }

    public function __toString(): string
    {
        return "($this->x, $this->y)[$this->energyLevel]";
    }
}
