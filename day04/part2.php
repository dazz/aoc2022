<?php

declare(strict_types=1);

use function Crell\fp\amap;
use function Crell\fp\pipe;
use function Crell\fp\explode;
use function Crell\fp\afilter;

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

function overlap(array $pairs): bool
{
    $first = explode('-')($pairs[0]);
    $second = explode('-')($pairs[1]);

    if (
        ($first[0] >= $second[0] && $first[0] <= $second[1]) || // first is in second
        ($first[1] >= $second[0] && $first[1] <= $second[1]) || // second is in first
        ($second[0] >= $first[0] && $second[0] <= $first[1]) || // first is in second
        ($second[1] >= $first[0] && $second[1] <= $first[1])
    ) {
        return true;
    }
    return false;
}


$result = pipe($inputFile,
    lines(...),
    amap(explode(',')),
    afilter(overlap(...)),
    count(...),
);

//print_r($result);
print $result . PHP_EOL;
