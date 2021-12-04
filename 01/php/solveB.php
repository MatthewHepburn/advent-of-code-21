<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';

$logger = new Logger();

$depths = (new InputLoader(__DIR__))->getAsInts();

$windowSize = 3;

function getWindowAt(int $i, array $array, int $windowSize) {
    return array_slice($array, $i, $windowSize);
}

$lastWindowSum = array_sum(getWindowAt(0, $depths, $windowSize));
$depthIncreases = 0;
for ($i = 1; $i < count($depths) - $windowSize + 1; $i++) {
    $windowSum = array_sum(getWindowAt($i, $depths, $windowSize));
    if ($windowSum > $lastWindowSum) {
        $depthIncreases += 1;
    }
    $logger->log("Window has depth $windowSum, last window was $lastWindowSum");
    $lastWindowSum = $windowSum;
}

echo $depthIncreases . PHP_EOL;
