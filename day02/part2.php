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
    return new Round(ShapeOpponent::from($o), Strategy::from($m));
}

// A for Rock, B for Paper, and C for Scissors
// X for Rock, Y for Paper, and Z for Scissors

// The score for a single round is the score for the shape you selected
// 1 for Rock, 2 for Paper, and 3 for Scissors
// plus the score for the outcome of the round
// (0 if you lost, 3 if the round was a draw, and 6 if you won)

// 2
// X means you need to lose,
// Y means you need to end the round in a draw, and
// Z means you need to win

//A Y => X
//B X => X
//C Z => X

enum ShapeOpponent: string
{
    case Rock = 'A';
    case Paper = 'B';
    case Scissors = 'C';
}

enum Strategy: string
{
    case lose = 'X';
    case draw = 'Y';
    case win = 'Z';
}

enum Hand: int
{
    case Rock = 1;
    case Paper = 2;
    case Scissors = 3;
}


class Round {
    public function __construct(
        public readonly ShapeOpponent $opponentHand,
        public readonly Strategy $strategy
    ) {}
}

class Score {
    public function __construct(public readonly int $value) {}
}

function turn(Score $score, Round $round): Score {

    $decisionMatrix = [
        ShapeOpponent::Rock->name => [
            Strategy::win->name => Hand::Paper,
            Strategy::lose->name => Hand::Scissors,
            Strategy::draw->name => Hand::Rock,
        ],
        ShapeOpponent::Paper->name => [
            Strategy::win->name => Hand::Scissors,
            Strategy::lose->name => Hand::Rock,
            Strategy::draw->name => Hand::Paper,
        ],
        ShapeOpponent::Scissors->name => [
            Strategy::win->name => Hand::Rock,
            Strategy::lose->name => Hand::Paper,
            Strategy::draw->name => Hand::Scissors,
        ],
    ];

    $scoreValue = match ($round->strategy) {
        Strategy::lose => 0,
        Strategy::draw => 3,
        Strategy::win => 6,
    };

    return new Score($score->value + $scoreValue + $decisionMatrix[$round->opponentHand->name][$round->strategy->name]->value);
}

$result = pipe($inputFile,
    lines(...),
    itmap(parse(...)),
    reduce(new Score(0), turn(...))
);

print $result->value . PHP_EOL;
