<?php

declare(strict_types=1);

use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\explode;
use function Crell\fp\implode;
use function Crell\fp\pipe;
use function Crell\fp\reduce;
use function Crell\fp\trace;

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

class Patch
{
    public function __construct(
        public readonly array $grid = [],
    ) {}
}

function addToGrid(Patch $patch, string $line): Patch
{
    return new Patch([
        ...$patch->grid,
        array_map(fn($height) => ['height' => (int) $height, 'visible' => null], str_split($line)),
    ]);
}

function findObstaclesInMyWay(array $way, int $me): bool {

    return pipe($way,
        afilter(fn($tree) => $tree['height'] >= $me),
        count(...),
        fn(int $countObstacles) => $countObstacles > 0,
    );
}

function isVisible(array $grid, int $rowKey, int $colKey): bool
{
    $colCount = count($grid[0]);
    $rowCount = count($grid);

    if ($rowKey === 0 || $colKey === 0) {
        return true;
    }
    if ($rowKey === $rowCount-1 || $colKey === $colCount-1) {
        return true;
    }

    $height = $grid[$rowKey][$colKey]['height'];

    // row => right
    $rowRight = array_reverse(array_slice($grid[$rowKey], $colKey+1,null, true));
    if (!findObstaclesInMyWay($rowRight, $height)) {
        return true;
    }

    // row <=> left
    $rowLeft = array_slice($grid[$rowKey], 0 , $colKey, true);
    if (!findObstaclesInMyWay($rowLeft, $height)) {
        return true;
    }

    $gridTransposed = pipe($grid,
        fn($lines) => array_map(null, ...$lines), // array_transpose
    );

    // col ^^ top
    $colTop = array_reverse(array_slice($gridTransposed[$colKey], $rowKey+1,null, true));
    if (!findObstaclesInMyWay($colTop, $height)) {
        return true;
    }

    // col vv bottom
    $colBottom = array_slice($gridTransposed[$colKey], 0 , $rowKey, true);
    if (!findObstaclesInMyWay($colBottom, $height)) {
        return true;
    }

    return false;
}

function makeVisible(array $grid): array
{
    foreach($grid as $rowKey => $trees) {
        foreach ($trees as $colKey => $tree) {
            $grid[$rowKey][$colKey] = [
                ...$tree,
                'visible' => $tree['visible'] ?: isVisible($grid, $rowKey, $colKey),
            ];
        }
    }

    return $grid;
}

function displayGrid(array $grid): string
{
    return pipe($grid,
        amap(fn($row) => pipe($row,
            amap(fn($tree) => match($tree['visible']) {
                true => 'O-'.$tree['height'],
                false => 'X-'.$tree['height'],
                null => '?-'.$tree['height'],
            }),
            implode(' ')
        )),
        implode(PHP_EOL),
    );
}

$result = pipe($inputFile,
    lines(...),
    reduce(new Patch(), addToGrid(...)),
    fn(Patch $patch) => $patch->grid,
    makeVisible(...),
//    displayGrid(...),
    amap(fn($row) => pipe($row,
        amap(fn($tree) => match($tree['visible']) {
            true => 1,
            false => 0,
        }),
        array_sum(...),
    )),
    array_sum(...),
);

//print_r($result) . PHP_EOL;
print $result . PHP_EOL;