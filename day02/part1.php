<?php

declare(strict_types=1);

use function Crell\fp\itmap;
use function Crell\fp\pipe;
use function Crell\fp\reduce;

require_once __DIR__ . '/../vendor/autoload.php';

$inputFile = __DIR__ . '/input.txt';

function lines(string $file): iterable
{
    $fp = fopen($file, 'rb');

    while ($line = fgets($fp)) {
        yield trim($line);
    }

    fclose($fp);
}

function parse(string $line): Round
{
    [$o, $m] = \explode(' ', $line);
    return new Round(ShapeOpponent::from($o), ShapeMine::from($m));
}

// A for Rock, B for Paper, and C for Scissors
// X for Rock, Y for Paper, and Z for Scissors

// The score for a single round is the score for the shape you selected
// 1 for Rock, 2 for Paper, and 3 for Scissors
// plus the score for the outcome of the round
// (0 if you lost, 3 if the round was a draw, and 6 if you won)

enum ShapeOpponent: string
{
    case Rock = 'A';
    case Paper = 'B';
    case Scissors = 'C';
}

enum ShapeMine: string
{
    case Rock = 'X';
    case Paper = 'Y';
    case Scissors = 'Z';
}

class Round {
    public function __construct(public readonly ShapeOpponent $opponentHand, public readonly ShapeMine $myHand) {}
}

class Score {
    public function __construct(public readonly int $value) {}
}

function turn(Score $score, Round $round): Score {

    $isDraw = fn($o, $m): bool => $o === $m;
    $haveWon = function($o, $m): bool {
        return (
            ($o == ShapeOpponent::Scissors && $m == ShapeMine::Rock) ||
            ($o == ShapeOpponent::Paper && $m == ShapeMine::Scissors) ||
            ($o == ShapeOpponent::Rock && $m == ShapeMine::Paper)
        );
    };

    $scoreValue = match (true) {
        $isDraw($round->opponentHand->name, $round->strategy->name) => 3,
        $haveWon($round->opponentHand, $round->strategy) => 6,
        default => 0,
    };

    $handValue = match($round->strategy) {
        ShapeMine::Rock => 1,
        ShapeMine::Paper => 2,
        ShapeMine::Scissors => 3,
    };

    return new Score($score->value + $scoreValue + $handValue);
}

$result = pipe($inputFile,
    lines(...),
    itmap(parse(...)),
    reduce(new Score(0), turn(...))
);

print $result->value . PHP_EOL;
