<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'vendor']);

return (new PhpCsFixer\Config())
    ->setRules([
        'binary_operator_spaces' => ['operators' => ['=' => 'align']],
    ])
    ->setFinder($finder);