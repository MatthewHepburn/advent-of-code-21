<?php

class LanternfishStates
{
    public array $stateCount;

    /**
     * @param int[] $startStates
     */
    public function __construct(array $startStates) {
        $this->stateCount = $this->getEmptyState();

        foreach ($startStates as $state) {
            $this->stateCount[$state] += 1;
        }
    }

    public function getTotalLanternfish(): int
    {
        return array_sum($this->stateCount);
    }

    public function advance()
    {
        $newState = $this->getEmptyState();

        // Fish at zero create new fish
        $newState[8] = $this->stateCount[0];
        // Fish at zero reset their own timer to 6
        $newState[6] = $this->stateCount[0];

        // All other fish simply decrement their counter
        for ($oldCounter = 1; $oldCounter < 9; $oldCounter++) {
            $newCounter = $oldCounter - 1;
            if (!isset($newState[$newCounter])) {
                $newState[$newCounter] = 0;
            }

            $newState[$newCounter] += $this->stateCount[$oldCounter];
        }

        $this->stateCount = $newState;
    }

    public function __toString(): string
    {
        $entries = [];
        foreach ($this->stateCount as $state => $count) {
            for ($i = 0; $i < $count; $i++) {
                $entries[]= (string) $state;
            }
        }

        return implode(',', $entries);
    }

    private function getEmptyState(): array
    {
        $state = [];
        for ($i = 0; $i < 9; $i++) {
            $state[$i] = 0;
        }

        return $state;
    }
}
