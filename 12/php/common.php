<?php

class CaveSystem
{
    private Cave $start;
    private Cave $end;
    private int $smallCaveDoubleCheckAllowance = 0;

    public function __construct(private array $caveMap, private Logger $logger)
    {
        $this->start = $this->caveMap['start'];
        $this->end = $this->caveMap['end'];
    }

    public function setSmallCaveDoubleCheckAllowance(int $smallCaveDoubleCheckAllowance): void
    {
        $this->smallCaveDoubleCheckAllowance = $smallCaveDoubleCheckAllowance;
    }

    /**
     * @return CaveRoute[]
     */
    public function findAllRoutesToEnd(): array
    {
        $newRoutes = [new CaveRoute($this->start)];
        $completeRoutes = [];
        while (count ($newRoutes) > 0 ) {
            $routes = $newRoutes;
            $newRoutes = [];
            foreach ($routes as $route) {
                foreach ($route->getCurrentEnd()->getConnectedCaves() as $connectedCave) {
                    if (!$this->canVisit($route, $connectedCave)) {
                        continue;
                    }

                    $clonedRoute = clone $route;
                    $clonedRoute->addStep($connectedCave);
                    if ($clonedRoute->getCurrentEnd()->getName() === $this->end->getName()) {
                        $completeRoutes[]= $clonedRoute;
                        $this->logger->log("Found route $clonedRoute");
                    } else {
                        $newRoutes[]= $clonedRoute;
                    }
                }
            }
        }

        return $completeRoutes;
    }

    private function canVisit(CaveRoute $route, Cave $cave): bool
    {
        if ($cave->getName() === $this->start->getName()) {
            return false;
        }

        if (!$cave->isSmall()) {
            return true;
        }

        if (!$route->hasVistedSmallCave($cave)) {
            return true;
        }

        return $route->getCountOfSmallCaveRevisits() < $this->smallCaveDoubleCheckAllowance;
    }

    public static function fromInput(array $lines, Logger $logger): static
    {
        $caveMap = [];
        foreach ($lines as $line) {
            $caveNames = explode('-', $line);
            if (count($caveNames) !== 2) {
                throw new Exception("Could not parse input line '$line'");
            }
            foreach ($caveNames as $caveName) {
                if (!isset($caveMap[$caveName])) {
                    $caveMap[$caveName] = new Cave($caveName);
                }
            }

            $cave1 = $caveMap[$caveNames[0]];
            $cave2 = $caveMap[$caveNames[1]];
            $cave1->addConnection($cave2);
            $cave2->addConnection($cave1);
        }

        return new CaveSystem($caveMap, $logger);
    }
}

class Cave
{
    /** @var array Cave[] */
    private array $connectedCaves = [];
    private bool $isSmall;

    public function __construct(private string $name)
    {
        $this->isSmall = $this->name === strtolower($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addConnection(Cave $cave): void
    {
        $this->connectedCaves[]= $cave;
    }

    public function isSmall(): bool
    {
        return $this->isSmall;
    }

    /**
     * @return Cave[]
     */
    public function getConnectedCaves(): array
    {
        return $this->connectedCaves;
    }
}

class CaveRoute
{
    private array $smallCavesVisitedMap = [];
    private array $sequence = [];
    private Cave $currentCave;
    private int $smallCaveRevisits = 0;

    public function __construct(private Cave $start)
    {
        $this->smallCavesVisitedMap[$start->getName()] = true;
        $this->sequence[]= $start->getName();
        $this->currentCave = $start;
    }

    public function hasVistedSmallCave(Cave $smallCave): bool
    {
        return isset($this->smallCavesVisitedMap[$smallCave->getName()]);
    }

    public function getCountOfSmallCaveRevisits(): int
    {
        return $this->smallCaveRevisits;
    }

    public function addStep(Cave $cave): void
    {
        $this->currentCave = $cave;
        $this->sequence[]= $cave->getName();
        if ($cave->isSmall()) {
            if (isset($this->smallCavesVisitedMap[$cave->getName()])) {
                $this->smallCaveRevisits += 1;
            } else {
                $this->smallCavesVisitedMap[$cave->getName()] = true;
            }
        }
    }

    public function getCurrentEnd(): Cave
    {
        return $this->currentCave;
    }

    public function __toString(): string
    {
        return implode(',', $this->sequence);
    }
}
