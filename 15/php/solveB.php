<?php

namespace AdventOfCode\TwentyOne\Chiton;

use InputLoader;
use Logger;

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

function normaliseRisk(int $risk): int {
    while ($risk >= 10) {
        $risk = $risk - 9;
    }

    return $risk;
}

$charArray = (new InputLoader(__DIR__))->getAsCharArray();
$intArraySmall = array_map(fn(array $a) => array_map(fn(string $x) => (int) $x, $a), $charArray);
$intArrayBig = [];
foreach ($intArraySmall as $line) {
    $newLine = [];
    for ($i = 0; $i < 5; $i++) {
        foreach ($line as $risk) {
            $newLine[]= normaliseRisk($risk + $i);
        }
    }
    $intArrayBig[]= $newLine;
}

$keys = array_keys($intArrayBig);
for ($i = 1; $i < 5; $i++) {
    foreach ($keys as $key) {
        $line = $intArrayBig[$key];
        $intArrayBig[]= array_map(fn(int $risk) => normaliseRisk($risk + $i), $line);
    }
}

$map = new Map($intArrayBig);

$logger->log($map->getRiskMap());

$step = 0;
while ($map->hasFrontier()) {
    $map->step();
    $step += 1;
    $logger->log("Step $step:\n{$map->getCostMap()}");
}

$solution = $map->getTravelRiskCost($map->getEnd());

echo $solution . "\n";
