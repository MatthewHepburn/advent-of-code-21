<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';

$logger = new Logger();

$instructionStrings = (new InputLoader(__DIR__))->getAsStrings();

$depth = 0;
$horizontalPos = 0;
$aim = 0;
foreach ($instructionStrings as $instruction) {
    list($direction, $distance) = explode(' ', $instruction);
    $logger->log("Moving $direction $distance places");
    switch ($direction) {
        case 'down':
            $aim += (int) $distance;
            break;
        case 'up':
            $aim -= (int) $distance;
            break;
        case 'forward':
            $horizontalPos += (int) $distance;
            $depth += ($aim * $distance);
            if ($depth < 0) {
                throw new Exception('Submarines can\'t fly!');
            }
            break;
        default:
            throw new Exception("Unknown direction: $direction");
    }
    $logger->log("New depth: $depth, new horizontal distance $distance");
}

echo ($depth * $horizontalPos) . PHP_EOL;
