<?php

namespace AdventOfCode\TwentyTwo\TrickShot;

use InputLoader;
use Logger;

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';

$logger = new Logger();

$line = (new InputLoader(__DIR__))->getAsStrings()[0];
$matches = null;
preg_match("/target area: x=([^.]*)..([^.]*), y=([^.]*)..([^.]*)/", $line, $matches);
$targetMinX = (int) $matches[1];
$targetMaxX = (int) $matches[2];

$targetMinY = (int)$matches[3];
$targetMaxY = (int)$matches[4];
$logger->log("X range = $targetMinX -> $targetMaxX");
$logger->log("Y range = $targetMaxY -> $targetMinY");

// Can put an upper bound on Vy0 of being at most |target min y|, or we would fall past the target before T = 1
$vx0 = 1;
$vy0 = abs($targetMinY);

$onTargetTrajectories = 0;

// Lower bound on vy0 of targetMinY, or we pass target in first step
while ($vy0 >= $targetMinY) {
    $logger->log("Considering trajectory ($vx0, $vy0)");

    $t = -1;
    $vx = $vx0;
    $vy = $vy0;
    $xPos = 0;
    $yPos = 0;
    $peak = $yPos;
    while (true) {
        $t++;
        $yPos += $vy;
        $xPos += $vx;

        $peak = max($yPos, $peak);

        $vx = $vx > 0 ? $vx - 1 : 0;
        $vy = $vy - 1;

        if ($yPos > $targetMaxY) {
            // Too high at T
            $logger->log("Too high at T = $t -> y = $yPos");
            continue;
        }
        if ($yPos < $targetMinY) {
            // Too low at T, gone too far, give up
            $logger->log("Too low at T = $t -> y = $yPos [Lower than $targetMinY]");
            break;
        }
        if ($xPos < $targetMinX) {
            // Too far left at T
            $logger->log("Too left at T = $t -> x = $xPos");
            continue;
        }
        if ($xPos > $targetMaxX) {
            // Too far right at T, gone too far, give up
            $logger->log("Too right at T = $t -> x = $xPos");
            break;
        }

        $logger->log("Found solution: Fire at ($vx0, $vy0), arrive at ($xPos, $yPos) at time $t");
        $onTargetTrajectories ++;
        break;
    }

    $vx0++;
    if ($vx0 > $targetMaxX) {
        $vx0 = 0;
        $vy0--;
    }
}

echo $onTargetTrajectories . "\n";
