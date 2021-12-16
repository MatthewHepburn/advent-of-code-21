<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$lines = (new InputLoader(__DIR__))->getAsStrings();
$polymer = array_shift($lines);

$pairRules = [];
foreach ($lines as $line) {
    $parts = explode(' -> ', $line);
    $pairRules[$parts[0]] = $parts[1];
}

for ($iteration = 1; $iteration <= ITERATIONS; $iteration++) {
    $newPolymer = '';
    for ($i = 0; $i < strlen($polymer); $i++) {
        $pair = substr($polymer, $i, 2);
        $newPolymer .= $pair[0];
        if (isset($pairRules[$pair])) {
            $newPolymer .=  $pairRules[$pair];
        }
    }

    $logger->log("Step $iteration: $polymer -> $newPolymer");
    $polymer = $newPolymer;
}

$frequencyMap = [];
foreach (str_split($polymer) as $char) {
    $frequencyMap[$char] = 1 + ($frequencyMap[$char] ?? 0);
}

$frequencies = array_values($frequencyMap);


echo max($frequencies) - min($frequencies) . PHP_EOL;
