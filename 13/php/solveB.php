<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/common.php';


$lines = (new InputLoader(__DIR__))->getAsStrings();
$pointLines = filter($lines, fn(string $line) => str_contains($line, ','));
$foldLines = filter($lines, fn(string $line) => str_contains($line, 'fold'));
$foldInstructions = array_map(fn(string $line) => FoldInstruction::fromInputLine($line), $foldLines);

$points = [];
foreach ($pointLines as $pointLine) {
    $point = FoldablePoint::fromInputLine($pointLine);
    foreach ($foldInstructions as $foldInstruction) {
        $point->fold($foldInstruction);
    }
    $points[]= $point;
}

$maxX = 0;
$maxY = 0;
foreach ($points as $point) {
    $maxX = max($maxX, $point->x);
    $maxY = max($maxY, $point->y);
}

$grid = [];
for ($i = 0; $i <= $maxX; $i++) {
    $grid[$i]=[];
}

foreach ($points as $point) {
    $grid[$point->x][$point->y] = true;
}

$output = '';
for ($y = 0; $y <= $maxY; $y++) {
    for ($x = 0; $x <= $maxX; $x++) {
        $output .= isset($grid[$x][$y]) ? '#' : ' ';
    }
    $output .= PHP_EOL;
}

echo $output;

