<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$binaryStrings = (new InputLoader(__DIR__))->getAsStrings();

function findValue(array $binaryStrings, callable $targetDecider, int $index)
{
    $ones = 0;
    $zeros = 0;
    foreach ($binaryStrings as $binaryString) {
        $bit = substr($binaryString, $index, 1);
        $bit === '1' ? $ones++ : $zeros++;
    }

    $targetBit = $targetDecider($ones, $zeros);
    $filteredBinaryStrings = [];
    foreach ($binaryStrings as $binaryString) {
        $bit = substr($binaryString, $index, 1);
        if ($bit === $targetBit) {
            $filteredBinaryStrings[]= $binaryString;
        }
    }
    if (count($filteredBinaryStrings) === 1) {
        return $filteredBinaryStrings[0];
    }
    if (count($filteredBinaryStrings) === 0) {
        throw new Exception('All strings eliminated');
    }

    return findValue($filteredBinaryStrings, $targetDecider, $index + 1);
}


$oxygenTargetDecider = function(int $ones, int $zeros) : string {
    return $ones >= $zeros ? '1' : '0';
};

$co2TargetDecider = function(int $ones, int $zeros) : string {
    return $zeros <= $ones ? '0' : '1';
};

$oxygenRatingBinary = findValue($binaryStrings, $oxygenTargetDecider, 0);
$co2ScrubberRatingBinary = findValue($binaryStrings, $co2TargetDecider, 0);

$logger->log("Oxygen generator rating binary = $oxygenRatingBinary");
$logger->log("CO2 scrubber rating binary = $co2ScrubberRatingBinary");

$oxygenRating = binaryPartsToDecimal(str_split($oxygenRatingBinary));
$co2ScrubberRating = binaryPartsToDecimal(str_split($co2ScrubberRatingBinary));

$logger->log("Oxygen generator rating = $oxygenRating");
$logger->log("CO2 scrubber rating = $co2ScrubberRating");

$solution = $oxygenRating * $co2ScrubberRating;
echo $solution . PHP_EOL;
