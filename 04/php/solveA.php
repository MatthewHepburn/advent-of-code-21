<?php

require_once __DIR__ . '/../../common/php/InputLoader.php';
require_once __DIR__ . '/../../common/php/Logger.php';
require_once __DIR__ . '/common.php';

$logger = new Logger();

$input = (new InputLoader(__DIR__))->getAsStrings();
$bingoNumbersAsString = array_shift($input);
$logger->log("Bingo numbers: $bingoNumbersAsString");
$bingoNumbers = explode(',', $bingoNumbersAsString);

$boards=[];
foreach (array_chunk($input, 5) as $inputChunk) {
    $board = new BingoBoard($inputChunk);
    $logger->log("Board:\n$board");
    $boards[]= $board;
}

$winningBoard = null;
$winningNumber = null;
foreach ($bingoNumbers as $bingoNumber) {
    $logger->log("Calling number $bingoNumber");
    foreach ($boards as $boardNumber => $board) {
        $hasNumber = $board->markNumber($bingoNumber);
        if ($hasNumber) {
            $logger->log("Board $boardNumber has a match:\n$board");
            if ($board->hasMarkedLine()) {
                $logger->log("Board $boardNumber has won:\n$board");
                $winningBoard = $board;
                $winningNumber = $bingoNumber;
                break 2;
            }
        }
    }
}

$solution = $winningBoard->getUnmarkedSum() * $winningNumber;
echo $solution . PHP_EOL;
