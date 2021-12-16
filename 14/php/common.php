<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$lines = (new InputLoader(__DIR__))->getAsStrings();
$polymers = [array_shift($lines) => 1];

$pairRules = [];
foreach ($lines as $line) {
    $parts = explode(' -> ', $line);
    $pairRules[$parts[0]] = $parts[1];
}

$frequencyMap = [];

for ($iteration = 1; $iteration <= ITERATIONS; $iteration++) {
    $newPolymers = [];
    foreach ($polymers as $polymer => $count) {
        $newPolymer = '';
        for ($i = 0; $i < strlen($polymer); $i++) {
            $pair = substr($polymer, $i, 2);
            $newPolymer .= $pair[0];
            if (isset($pairRules[$pair])) {
                $newPolymer .=  $pairRules[$pair];
            }
        }

//        $logger->log("Step $iteration: $polymer -> $newPolymer");
        $newPolymers[$newPolymer] = $count + ($newPolymers[$newPolymer] ?? 0);
    }

    $polymers = $newPolymers;

    // See if we can split any of our polymers
    $newPolymers = [];
    foreach ($polymers as $polymer => $count) {
        $polymerLength = strlen($polymer);
        if ($polymerLength < 5 || $polymerLength % 2 == 0) {
            // Skip short polymers and even length polymers
            $newPolymers[$polymer] = $count;
            continue;
        }

        $middlePos = intdiv($polymerLength, 2);
        $middleChar = $polymer[$middlePos];

        $start = substr($polymer, 0, $middlePos + 1);
        $end = substr($polymer, $middlePos);

        // We're double counting the middle entry here, so subtract one set from the frequency map
        $frequencyMap[$middleChar] = (-1 * $count) + ($frequencyMap[$middleChar] ?? 0);
        $newPolymers[$start] = $count + ($newPolymers[$start] ?? 0);
        $newPolymers[$end] = $count + ($newPolymers[$end] ?? 0);

        $logger->log("Split $polymer into $start and $end");
    }
    $polymers = $newPolymers;
    $logger->log("Map: " . json_encode($polymers));
}


foreach ($polymers as $polymer => $count) {
    foreach (str_split($polymer) as $char) {
        $frequencyMap[$char] = $count + ($frequencyMap[$char] ?? 0);
    }
}


$frequencies = array_values($frequencyMap);


echo max($frequencies) - min($frequencies) . PHP_EOL;
