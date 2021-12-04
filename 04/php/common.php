<?php

class BingoBoard
{
    /** @var BoardNumber[][] */
    private array $numbers;

    public function __construct(array $input)
    {
        $this->numbers = [];
        foreach ($input as $rowString) {
            $parsedRow=[];
            $row = explode(' ', $rowString);
            foreach ($row as $number) {
                if ($number === '') {
                    // There may be multiple spaces between numbers
                    continue;
                }
                $parsedRow[]= new BoardNumber((int) $number);
            }
            $this->numbers[]=$parsedRow;
        }
    }

    public function markNumber(int $number): bool
    {
        foreach ($this->numbers as $row) {
            foreach ($row as $boardNumber) {
                if ($boardNumber->getNumber() === $number) {
                    $boardNumber->mark();
                    return true;
                }
            }
        }

        return false;
    }

    public function getUnmarkedSum(): int
    {
        $sum = 0;
        foreach ($this->numbers as $row) {
            foreach ($row as $number) {
                if (!$number->isMarked()) {
                    $sum += $number->getNumber();
                }
            }
        }

        return $sum;
    }

    public function hasMarkedLine(): bool
    {
        return $this->hasMarkedRow() || $this->hasMarkedColumn();
    }

    private function hasMarkedRow(): bool
    {
        foreach ($this->numbers as $row) {
            $allMarked = true;
            foreach ($row as $number) {
                if (!$number->isMarked()) {
                    $allMarked = false;
                    break;
                }
            }

            if ($allMarked) {
                return true;
            }
        }
        return false;
    }

    private function hasMarkedColumn(): bool
    {
        $columnCount = count($this->numbers[0]);
        for ($i = 0; $i < $columnCount; $i++) {
            $allMarked = true;
            foreach ($this->numbers as $row) {
                $number = $row[$i];
                if (!$number->isMarked()) {
                    $allMarked = false;
                    break;
                }
            }

            if ($allMarked) {
                return true;
            }
        }

        return false;
    }

    public function __toString(): string
    {
        $output='';
        foreach ($this->numbers as $row) {
            $output .= implode(' ', $row) . "\n";
        }
        return $output;
    }
}

class BoardNumber
{
    private bool $marked = false;

    public function __construct(private int $number) { }

    public function isMarked(): bool
    {
        return $this->marked;
    }

    public function mark()
    {
        $this->marked = true;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function __toString(): string
    {
        if (!$this->isMarked()) {
            return str_pad($this->number, 4, ' ', STR_PAD_RIGHT);
        }

        return str_pad("({$this->number})", 4, ' ', STR_PAD_RIGHT);
    }
}
