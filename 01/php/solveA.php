<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';

$depths = (new InputLoader(__DIR__))->getAsInts();

$lastDepth = array_shift($depths);
$depthIncreases = 0;
foreach ($depths as $depth) {
    if ($depth > $lastDepth) {
        $depthIncreases += 1;
    }
    $lastDepth = $depth;
}

echo $depthIncreases . PHP_EOL;
