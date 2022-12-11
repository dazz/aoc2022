<?php

declare(strict_types=1);

use function Crell\fp\amap;
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

function chunk3(array $array): array
{
    return array_chunk($array, 3);
}

function priority(string $char): int
{
    $priorities = array_combine(
        str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
        range(1, 52)
    );
    return $priorities[$char];
}

$result = pipe($inputFile,
    file_get_contents(...),
    static fn (string $string): array => explode(PHP_EOL, $string),
    amap(str_split(...)),
    chunk3(...),
    amap(fn($rucksacks) => array_intersect(...$rucksacks)),
    amap(array_unique(...)),
    amap(array_values(...)),
    amap(fn($charArray) => amap(fn($char) => priority($char))($charArray)),
    amap(array_sum(...)),
    array_sum(...),
);

//print_r($result);

print $result . PHP_EOL;
