<?php

declare(strict_types=1);

use function Crell\fp\explode;
use function Crell\fp\pipe;

require_once __DIR__ . '/../vendor/autoload.php';

$inputFile = __DIR__ . '/input.txt';

function caloriesByElv(array $values): array
{
    $elves = [];
    $i = 0;

    foreach ($values as $value) {
        if ($value === "") {
            $i++;
            continue;
        }
        $elves[$i] ??= (int) 0;
        $elves[$i] += $value;
    }

    return $elves;
}

function sortValues(array $values): array
{
    sort($values);
    return $values;
}

function highest3(array $values): array
{
    return array_slice($values, -3);
}

$result = pipe($inputFile,
    file_get_contents(...),
    trim(...),
    explode(PHP_EOL),
    caloriesByElv(...),
    sortValues(...),
    highest3(...),
    array_sum(...),
);

print $result . PHP_EOL;
