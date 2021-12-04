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

$lastBoard = null;
$lastBoardFinalNumber = null;
foreach ($bingoNumbers as $bingoNumber) {
    $logger->log("Calling number $bingoNumber");

    $remainingBoards = [];

    foreach ($boards as $boardNumber => $board) {
        $hasNumber = $board->markNumber($bingoNumber);
        $hasWon = false;
        if ($hasNumber) {
            $logger->log("Board $boardNumber has a match:\n$board");
            if ($board->hasMarkedLine()) {
                $logger->log("Board $boardNumber has a line:\n$board");
                $hasWon = true;
            }
        }

        if ($hasWon && count($boards) === 1) {
            $logger->log("Last board ($boardNumber) has won:\n$board");
            $lastBoard = $board;
            $lastBoardFinalNumber = $bingoNumber;
            break 2;
        }

        if (!$hasWon) {
            $remainingBoards[$boardNumber] = $board;
        }
    }

    $boards = $remainingBoards;
}

$solution = $lastBoard->getUnmarkedSum() * $lastBoardFinalNumber;
echo $solution . PHP_EOL;
