<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$inputStrings = (new InputLoader(__DIR__))->getAsStrings();

$pointFrequencies = [];
foreach ($inputStrings as $inputString) {
    $line = Line::fromInput($inputString);
    $points = [];
    if ($line->isHorizontal()) {
        $points = getPointsOnHorizontalLine($line);
        $logger->log("Horizontal line {$line} has points: " . implode(' , ', $points));
    } elseif ($line->isVertical()) {
        $points = getPointsOnVerticalLine($line);
        $logger->log("Vertical line {$line} has points: " . implode(' , ', $points));
    }

    foreach ($points as $point) {
        $pointStr = (string) $point;
        $prevPointFrequency = $pointFrequencies[$pointStr] ?? 0;
        $pointFrequencies[$pointStr] = $prevPointFrequency + 1;
    }
}

$logger->log("Point frequencies:\n" . json_encode($pointFrequencies, JSON_PRETTY_PRINT));

$dangerPoints = 0;
foreach ($pointFrequencies as $frequency) {
    if ($frequency > 1) {
        $dangerPoints += 1;
    }
}

echo $dangerPoints . PHP_EOL;
