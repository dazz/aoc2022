<?php

declare(strict_types=1);

use function Crell\fp\amap;
use function Crell\fp\pipe;
use function Crell\fp\explode;
use function Crell\fp\afilter;
use function Crell\fp\reduce;
use function Crell\fp\reduceUntil;
use function Crell\fp\trace;

require_once __DIR__ . '/../vendor/autoload.php';

$inputFile = __DIR__ . '/input.txt';

function findTheMarker(string $characters): int
{
    $position = 14;
    $numberOfCharacters = 14;
    while (true) {
        $stopSearching = pipe($characters,
            fn(string $characters) => substr($characters, $position-$numberOfCharacters, $numberOfCharacters),
            fn(string $subsctring) => str_split($subsctring),
            fn(array $stringArray) => array_unique($stringArray),
            fn(array $uniqueCharacters) => count($uniqueCharacters),
            fn(int $numberOfUniqueChracters) => $numberOfUniqueChracters === $numberOfCharacters
        );
        if ($stopSearching) {
            return $position;
        }
        $position++;
    }
    return $position;
}

$result = pipe($inputFile,
    file_get_contents(...),
    findTheMarker(...)
);

//print_r($result) . PHP_EOL;
print $result . PHP_EOL;