<?php

declare(strict_types=1);

if (true === (require_once __DIR__.'/../vendor/autoload.php') || empty($_SERVER['SCRIPT_FILENAME'])) {
    return;
}

$host = $argv[1] ?? null;
$port = $argv[2] ?? 25565;

if (!isset($host)) {
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

$closure($host, $port);
