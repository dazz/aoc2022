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

function findTheMarker(int $position, string $characters): int
{
    $numberOfCharacters = 4;
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
    fn(string $characters) => findTheMarker(4, $characters)
);

//print_r($result) . PHP_EOL;
print $result . PHP_EOL;