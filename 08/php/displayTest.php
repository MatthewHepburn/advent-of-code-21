<?php

require_once __DIR__ . '/common.php';

for ($i = 0; $i < 10; $i++) {
    echo "$i:\n";
    $display = new SevenSegmentDisplay($i);
    echo $display . "\n";
}
