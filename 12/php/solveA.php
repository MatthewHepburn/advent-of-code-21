<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$lines = (new InputLoader(__DIR__))->getAsStrings();
$caveSystem = CaveSystem::fromInput($lines, $logger);

$routes = $caveSystem->findAllRoutesToEnd();
$logger->log("Found " . count($routes) . " routes");
foreach ($routes as $route) {
    $logger->log($route);
}

echo count($routes) . PHP_EOL;
