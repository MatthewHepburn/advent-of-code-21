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

$allDigitMap = getSegmentCountMap();

$logger->log("Easy digit segment counts:\n" . json_encode($easyDigitMap, JSON_PRETTY_PRINT));


$outputSum = 0;
foreach ($observations as $observation) {
    $wireMapping = new WireMapping();

    $logger->log("Considering outputs: " . json_encode($observation->getOutputs()));

    foreach ($observation->getSignals() as $signal) {
        $signalLength = count($signal);
        foreach ($easyDigitMap as $digit => $segmentCount) {
            if ($signalLength === $segmentCount) {
                $wireMapping->setWiresForDigit($signal, $digit);
            }
        }
    }

    $logger->log("After easy digits, mapping:\n$wireMapping");

    foreach ($observation->getSignals() as $signal) {
        $signalLength = count($signal);
        $possibleDigits = [];
        foreach ($allDigitMap as $digit => $segmentCount) {
            if ($signalLength === $segmentCount) {
                $possibleDigits[]= $digit;
            }
        }

        $wireMapping->removeInvalidFromPossible($signal, $possibleDigits);
    }

    $logger->log("After second pass, mapping:\n$wireMapping");

    $wireMapping->completeDeduction();
    $logger->log("After final step, mapping:\n$wireMapping");

    $observationValue = 0;
    $powersOfTen = 1;
    foreach (array_reverse($observation->getOutputs()) as $segments) {
        $digit = $wireMapping->getDigitFromOutput($segments);
        $observationValue += $powersOfTen * $digit;
        $powersOfTen *= 10;
    }

    $outputSum += $observationValue;
    $logger->log("Observation value = $observationValue");
}

echo $outputSum . PHP_EOL;
