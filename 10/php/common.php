<?php

class BracketClassifier
{
    private array $errorScoreMap = [
        ')' => 3,
        ']' => 57,
        '}' => 1197,
        '>' => 25137
    ];

    private array $autocompleteScoreMap = [
        ')' => 1,
        ']' => 2,
        '}' => 3,
        '>' => 4
    ];

    public function isMatchingPair(string $openBracket, string $closeBracket): bool
    {
        return match($openBracket) {
            '(' => $closeBracket === ')',
            '[' => $closeBracket === ']',
            '{' => $closeBracket === '}',
            '<' => $closeBracket === '>',
            default => throw new Exception("Unknown open bracket found '$openBracket'")
        };
    }

    public function getCloseBracket(mixed $openBracket): string
    {
        return match($openBracket) {
            '(' => ')',
            '[' => ']',
            '{' => '}',
            '<' => '>',
            default => throw new Exception("Unknown open bracket found '$openBracket'")
        };
    }


    public function isOpen(string $bracket): bool {
        return !isset($this->errorScoreMap[$bracket]);
    }

    public function getErrorScore(string $closingBracket): int {
        return $this->errorScoreMap[$closingBracket];
    }

    public function getAutocompleteScore(string $closingBracket): int {
        return $this->autocompleteScoreMap[$closingBracket];
    }
}
