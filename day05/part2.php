<?php

declare(strict_types=1);

use function Crell\fp\amap;
use function Crell\fp\pipe;
use function Crell\fp\explode;
use function Crell\fp\afilter;
use function Crell\fp\reduce;
use function Crell\fp\implode;

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

class Harbor {
    public function __construct(public readonly array $stacks)
    {
    }
}

function aPop(array $array): array
{
    array_pop($array);
    return $array;
}

function parseStartingStacks(string $stacksString): Harbor
{
    $stacks = pipe($stacksString,
        explode(PHP_EOL),
        amap(fn($line) => str_split($line, 4)),
        aPop(...),
        array_reverse(...),
        fn($lines) => array_map(null, ...$lines), // array_transpose
        amap(fn($stack) => afilter(fn($container) => !empty(trim($container)))($stack))
    );
    return new Harbor($stacks);
}

function step(Harbor $harbor, string $command): Harbor
{
    $pattern = '/move (?<count>\d+) from (?<from>\d+) to (?<to>\d+)/';
    preg_match($pattern, $command, $matches);

    $stacks = $harbor->stacks;

    array_push($stacks[$matches['to']-1], ...array_splice($stacks[$matches['from']-1], -($matches['count'])));

    return new Harbor($stacks);
}

$setup = pipe($inputFile,
    file_get_contents(...),
    explode(PHP_EOL.PHP_EOL),
);

$stacks = parseStartingStacks($setup[0]);

$result  = pipe($setup[1],
    explode(PHP_EOL),
    reduce($stacks, step(...)),
    fn(Harbor $harbor) => $harbor->stacks,
    amap(array_pop(...)),
    implode(''),
    fn($value) => str_replace([' ', '[', ']'], [''], $value)
);

//print_r($stacks);
//print_r($result) . PHP_EOL;
print $result . PHP_EOL;