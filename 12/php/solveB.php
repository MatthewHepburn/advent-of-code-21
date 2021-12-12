<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

// Who needs space efficiency?
ini_set('memory_limit', '512M');

$lines = (new InputLoader(__DIR__))->getAsStrings();
$caveSystem = CaveSystem::fromInput($lines, $logger);
$caveSystem->setSmallCaveDoubleCheckAllowance(1);

$routes = $caveSystem->findAllRoutesToEnd();
$logger->log("Found " . count($routes) . " routes");
foreach ($routes as $route) {
    $logger->log($route);
}

echo count($routes) . PHP_EOL;
