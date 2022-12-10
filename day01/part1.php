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


$result = pipe($inputFile,
    file_get_contents(...),
    trim(...),
    explode(PHP_EOL),
    caloriesByElv(...),
    max(...),
);

print $result . PHP_EOL;
