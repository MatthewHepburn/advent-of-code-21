<?php

namespace AdventOfCode\TwentyTwo\PacketDecoder;

use Logger;

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/StandardLib.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$values = [
    'C200B40A82' => 3,
    '04005AC33890' => 54,
    '880086C3E88112' => 7,
    'CE00C43D881120' => 9,
    'D8005AC2A8F0' => 1,
    'F600BC2D8F' => 0,
    '9C005AC2F8F0' => 0,
    '9C0141080250320F1802104A08' => 1
];

foreach ($values as $input => $expected) {
    $packetValue = Transmission::fromHex($input, $logger)->readPacket()->getValue();
    if ($packetValue === $expected) {
        $logger->log("[x] $input => $expected");
    } else {
        $logger->log("ERR for $input expected $expected, got $packetValue");
    }
}
