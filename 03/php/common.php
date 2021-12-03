<?php

function binaryPartsToDecimal(array $parts): int
{
    $value = 0;
    $powerOfTwo = 1;
    foreach (array_reverse($parts) as $part) {
        $value += ((int) $part) * $powerOfTwo;
        $powerOfTwo = $powerOfTwo * 2;
    }

    return $value;
}
