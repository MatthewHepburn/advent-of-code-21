<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();
$classifier = new BracketClassifier();

$lines = (new InputLoader(__DIR__))->getAsStrings();


$errorScore = 0;

foreach ($lines as $line) {
    $bracketStack = [];
    foreach (str_split($line) as $char) {
        if ($classifier->isOpen($char)) {
            array_push($bracketStack, $char);
        } else {
            $openBracket = array_pop($bracketStack);
            if (!$classifier->isMatchingPair($openBracket, $char)) {
                $logger->log("Found bracket mismatch: '$openBracket' vs '$char'");
                $errorScore += $classifier->getErrorScore($char);
                break;
            }
        }
    }
}

echo $errorScore . PHP_EOL;
