<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();
$classifier = new BracketClassifier();

$lines = (new InputLoader(__DIR__))->getAsStrings();


$autocompleteScores = [];

foreach ($lines as $line) {
    $bracketStack = [];
    foreach (str_split($line) as $char) {
        if ($classifier->isOpen($char)) {
            array_push($bracketStack, $char);
        } else {
            $openBracket = array_pop($bracketStack);
            if (!$classifier->isMatchingPair($openBracket, $char)) {
                continue 2;
            }
        }
    }

    $autocompleteScore = 0;
    while ($openBracket = array_pop($bracketStack)) {
        $closeBracket = $classifier->getCloseBracket($openBracket);

        $autocompleteScore *= 5;
        $autocompleteScore += $classifier->getAutocompleteScore($closeBracket);
    }
    $autocompleteScores[]= $autocompleteScore;
}

sort($autocompleteScores);
$medianPoint = intdiv(count($autocompleteScores), 2);
$medianAutocompleteScore = $autocompleteScores[$medianPoint];

echo $medianAutocompleteScore . PHP_EOL;
