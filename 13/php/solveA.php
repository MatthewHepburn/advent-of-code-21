<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/common.php';

$lines = (new InputLoader(__DIR__))->getAsStrings();
$pointLines = filter($lines, fn(string $line) => str_contains($line, ','));
$foldLines = filter($lines, fn(string $line) => str_contains($line, 'fold'));
$foldInstructions = array_map(fn(string $line) => FoldInstruction::fromInputLine($line), $foldLines);

// Consider only the first fold instruction
$foldInstructions = [$foldInstructions[0]];

$pointMap = [];
foreach ($pointLines as $pointLine) {
    $point = FoldablePoint::fromInputLine($pointLine);
    foreach ($foldInstructions as $foldInstruction) {
        $point->fold($foldInstruction);
    }
    $pointMap[(string) $point] = true;
}

echo count($pointMap) . PHP_EOL;
