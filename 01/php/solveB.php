<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';

$depths = (new InputLoader(__DIR__))->getAsInts();

$windowSize = 3;

function getWindowAt(int $i, array $array, int $windowSize) {
    return array_slice($array, $i, $windowSize);
}

$lastWindowSum = array_sum(getWindowAt(0, $depths, $windowSize));
$depthIncreases = 0;
for ($i = 1; $i < count($depths) - $windowSize; $i++) {
    $windowSum = array_sum(getWindowAt($i, $depths, $windowSize));
    if ($windowSum > $lastWindowSum) {
        $depthIncreases += 1;
    }
    $lastWindowSum = $windowSum;
}

echo $depthIncreases . PHP_EOL;
