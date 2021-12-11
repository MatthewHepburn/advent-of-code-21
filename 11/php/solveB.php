<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$lines = (new InputLoader(__DIR__))->getAsStrings();
$octopusGrid = OctopusGrid::fromInput($lines);

$logger->log("Before any steps:\n$octopusGrid");

$octoCount = $octopusGrid->getOctoCount();
for ($i = 1; true; $i++) {
    $flashes = $octopusGrid->step();
    $logger->log("After step $i:\n$octopusGrid");

    if ($flashes === $octoCount) {
        break;
    }
}

echo $i . PHP_EOL;
