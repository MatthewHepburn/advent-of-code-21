<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$inputString = (new InputLoader(__DIR__))->getAsStrings()[0];

$state = new LanternfishStates(explode(',', $inputString));

$logger->log("Initial state: $state");

for ($i = 0; $i < 80; $i++) {
    $state->advance();
    $logger->log("State: $state");
}

echo $state->getTotalLanternfish() . PHP_EOL;
