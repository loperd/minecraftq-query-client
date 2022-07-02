<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude(['vendor', 'tools'])
    ->notPath('.php-cs-fixer.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12' => true,
    'strict_param' => true,
    'modernize_strpos' => true,
    'use_arrow_functions' => false,
    'array_syntax' => ['syntax' => 'short'],
])->setFinder($finder);