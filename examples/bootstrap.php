<?php

declare(strict_types=1);

if (true === (require_once __DIR__.'/../vendor/autoload.php') || empty($_SERVER['SCRIPT_FILENAME'])) {
    return;
}

function resolveHost(array $argv): string
{
    return explode(':', $argv[1] ?? '')[0] ?? '';
}

function resolvePort(array $argv): int
{
    $type = explode('-', basename($_SERVER['SCRIPT_FILENAME']))[0];
    $defaultServerPort = 'bedrock' === $type ? 19132 : 25565;

    return (int) (explode(':', $argv[1] ?? '')[1]
        ?? $argv[2]
        ?? $defaultServerPort);
}

$host = resolveHost($argv);
$port = resolvePort($argv);

if ('' === $host) {
    echo PHP_EOL;
    \printf("Usage: php %s <host> <port>\n", $_SERVER['SCRIPT_FILENAME']);
    echo PHP_EOL;
    exit;
}

$closure = require $_SERVER['SCRIPT_FILENAME'];
if (!\is_callable($closure)) {
    throw new TypeError(\sprintf(
        'Invalid return value: callable object expected, "%s" returned from "%s".',
        \get_debug_type($closure),
        $_SERVER['SCRIPT_FILENAME']
    ));
}

printf("\n[INFO] Starting ping server by the: %s:%s\n\n", $host, $port);

$closure($host, $port);
