<?php

namespace AdventOfCode\TwentyOne\Chiton;

use InputLoader;
use Logger;

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$charArray = (new InputLoader(__DIR__))->getAsCharArray();
$intArray = array_map(fn(array $a) => array_map(fn(string $x) => (int) $x, $a), $charArray);
$map = new Map($intArray);


$step = 0;
while ($map->hasFrontier()) {
    $map->step();
    $step += 1;
    $logger->log("Step $step:\n{$map->getCostMap()}");
}

$solution = $map->getTravelRiskCost($map->getEnd());

echo $solution . "\n";
