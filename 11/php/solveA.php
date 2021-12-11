<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$lines = (new InputLoader(__DIR__))->getAsStrings();
$octopusGrid = OctopusGrid::fromInput($lines);

$logger->log("Before any steps:\n$octopusGrid");

$totalFlashes = 0;
for ($i = 1; $i <= 100; $i++) {
    $totalFlashes += $octopusGrid->step();
    $logger->log("After step $i:\n$octopusGrid");
}

echo $totalFlashes . PHP_EOL;
