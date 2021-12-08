<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$inputStrings = (new InputLoader(__DIR__))->getAsStrings();
$observations = array_map(fn(string $x) => Observation::fromInputLine($x), $inputStrings);

$easyDigits = [1, 4, 7, 8];
$easyDigitMap = [];
foreach ($easyDigits as $easyDigit) {
    $easyDigitMap[$easyDigit] = count((new SevenSegmentDisplay($easyDigit))->getIlluminatedSegments());
}

$logger->log("Easy digit segment counts:\n" . json_encode($easyDigitMap, JSON_PRETTY_PRINT));


$easyDigitsSeen = 0;
foreach ($observations as $observation) {
    foreach ($observation->getOutputs() as $output) {
        $outputLength = count($output);
        foreach ($easyDigitMap as $digit => $segmentCount) {
            if ($outputLength === $segmentCount) {
                $easyDigitsSeen += 1;
            }
        }
    }
}

echo $easyDigitsSeen . PHP_EOL;
