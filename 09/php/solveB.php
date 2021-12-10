<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$inputStrings = (new InputLoader(__DIR__))->getAsStrings();
$map = Map::fromInput($inputStrings);
$minima = $map->getMinima();

$basinSizes = [];

foreach ($minima as $minimum) {
    $basinPoints = [
        (string) $minimum => true
    ];

    $nextPoints = [$minimum];
    while (count($nextPoints) > 0) {
        $thesePoints = $nextPoints;
        $nextPoints = [];
        foreach ($thesePoints as $point) {
            $validNeighbours = $map->getNeighbouringBasinPointsAbove($point);
            foreach ($validNeighbours as $validNeighbour) {
                $key = (string) $validNeighbour;
                if (!isset($basinPoints[$key])) {
                    $nextPoints[]= $validNeighbour;
                    $basinPoints[$key] = true;
                }
            }
        }
    }

    $basinSize = count($basinPoints);
    $logger->log("Found basin of size $basinSize");
    $basinSizes[]= $basinSize;
}

rsort($basinSizes);
$solution = $basinSizes[0] * $basinSizes[1] * $basinSizes[2];

echo $solution . PHP_EOL;
