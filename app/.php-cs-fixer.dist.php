<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['vendor', 'resources'])
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@Symfony' => true,
        'yoda_style' => false,
    ])
    ->setFinder($finder);