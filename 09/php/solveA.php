<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$inputStrings = (new InputLoader(__DIR__))->getAsStrings();
$map = Map::fromInput($inputStrings);

$minima = $map->getMinima();

$riskSum = array_sum(array_map(fn(Point $x) => 1 + $x->value, $minima));

echo $riskSum . PHP_EOL;
