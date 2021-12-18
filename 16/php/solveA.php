<?php

namespace AdventOfCode\TwentyTwo\PacketDecoder;

use InputLoader;
use Logger;

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$hexString = (new InputLoader(__DIR__))->getAsStrings();
$transmission = Transmission::fromHex($hexString[0], $logger);

$packet = $transmission->readPacket();
$logger->log($transmission);
$logger->log($packet);


$indentation = "\t";
$frontier = $packet->getPackets();
$newFrontier = [];
while (count($frontier) > 0) {
    foreach ($frontier as $subPacket) {
        $logger->log("{$indentation}$subPacket");
        $newFrontier = array_merge($newFrontier, $subPacket->getPackets());
    }

    $frontier = $newFrontier;
    $newFrontier = [];
    $indentation .= "\t";
}

echo $packet->getVersionSum() . "\n";
