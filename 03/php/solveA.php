<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$binaryStrings = (new InputLoader(__DIR__))->getAsStrings();
$stringLength = strlen($binaryStrings[0]);

$gammaRateParts=[];
$epsilonRateParts=[];
for ($i = 0; $i < $stringLength; $i++) {
    $ones = 0;
    $zeros = 0;
    foreach ($binaryStrings as $binaryString) {
        $char = substr($binaryString, $i, 1);
        $char === '1' ? $ones++ : $zeros++;
    }
    if ($ones === $zeros) {
        throw new Exception('Tie!');
    }
    $gammaRateParts[]= $ones > $zeros ? '1' : '0';
    $epsilonRateParts[]= $ones < $zeros ? '1' : '0';
}


$gammaRateBinaryString = implode('', $gammaRateParts);
$logger->log("Gamma rate binary = $gammaRateBinaryString");
$epsilonRateBinaryString = implode('', $epsilonRateParts);
$logger->log("Epsilon rate binary = $epsilonRateBinaryString");

$gammaRate = binaryPartsToDecimal($gammaRateParts);
$epsilonRate = binaryPartsToDecimal($epsilonRateParts);

$logger->log("Gamma rate decimal = $gammaRate");
$logger->log("Epsilon rate decimal = $epsilonRate");

$solution = $epsilonRate * $gammaRate;
echo $solution . PHP_EOL;
