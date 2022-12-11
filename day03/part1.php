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

function priority(string $char): int
{
    $priorities = array_combine(
        str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
        range(1, 52)
    );
    return $priorities[$char];
}

$result = pipe($inputFile,
    lines(...),
    amap(str_split(...)),
    amap(fn($line) => array_chunk($line, count($line)/2)),
    amap(fn($compartments) => array_intersect(...$compartments)),
    amap(array_unique(...)),
    amap(array_values(...)),
    amap(fn($charArray) => amap(fn($char) => priority($char))($charArray)),
    amap(array_sum(...)),
    array_sum(...),
);

//print_r($result);

print $result . PHP_EOL;
