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

class System
{
    public function __construct(
        public readonly array $tree = [],
        public readonly string $currentPath = '/',
    ) {}

}

function pop(array $array): mixed
{
    return array_pop($array);
}

function walk(System $system, $line): System
{
    $tree = $system->tree;

    $currentPath = match (true) {
        $line === '$ cd /' => '/',
        $line === '$ cd ..' => $tree[$system->currentPath]['parent'],
        str_contains($line, '$ cd ') => pipe($line,
            explode(' '),
            pop(...),
            fn(string $dirName) => $system->currentPath . $dirName . DIRECTORY_SEPARATOR,
        ),
        default => $system->currentPath,
    };

    $tree[$currentPath] ??= ['size' => 0, 'parent' => null];

    if ($currentPath !== $system->currentPath && str_contains($currentPath, $system->currentPath)) {
        $tree[$currentPath]['parent'] = $system->currentPath;
    }

    $pattern = '/(?<size>\d+) (?<file>[\.\w]+)/';
    if (preg_match($pattern, $line, $matches) === 1) {
        $tree = updateSize($tree, $currentPath, $matches['size']);
    }

    return new System($tree, $currentPath);
}

function updateSize($tree, $path, $size): array
{
    $tree[$path]['size'] += $size;

    if ($tree[$path]['parent'] === null) {
        return $tree;
    }
    return updateSize($tree, $tree[$path]['parent'], $size);
}

$result = pipe($inputFile,
    lines(...),
    reduce(new System(), walk(...)),
    fn(System $system) => $system->tree,
    afilter(fn($dir) => $dir['size'] < 100000),
    amap(fn($dir) => $dir['size']),
    array_sum(...),
);

//print_r($result) . PHP_EOL;
print $result . PHP_EOL;