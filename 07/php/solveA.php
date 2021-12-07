<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';

$logger = new Logger();

$inputString = (new InputLoader(__DIR__))->getAsStrings()[0];

$positions = array_map(fn($x) => (int) $x, explode(',', $inputString));
$positionFrequencies = [];

$minPosition = $positions[0];
$maxPosition = $positions[0];
foreach ($positions as $position) {
    $curCount = $positionFrequencies[$position] ?? 0;
    $positionFrequencies[$position] = $curCount + 1;

    $minPosition = min($minPosition, $position);
    $maxPosition = max($maxPosition, $position);
}

function getCost($targetPosition, $positionFrequencies)
{
    $cost = 0;
    foreach ($positionFrequencies as $position => $frequency) {
        $distance = abs($position - $targetPosition);
        $cost += $distance * $frequency;
    }

    return $cost;
}

$costMap = [];
$minCost = null;
$minCostPosition = null;
for ($targetPosition = $minPosition; $targetPosition <= $maxPosition; $targetPosition++) {
    $cost = getCost($targetPosition, $positionFrequencies);
    $costMap[$targetPosition] = $cost;

    if (!$minCost || $minCost > $cost) {
        $minCost = $cost;
        $minCostPosition = $targetPosition;
    }
}

$logger->log("Min cost if crabs move to position $minCostPosition");

echo $minCost . PHP_EOL;
