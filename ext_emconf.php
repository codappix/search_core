<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Search Core',
    'description' => 'Search core for implementing various search types.',
    'category' => 'be',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.0-6.2.99',
            'php' => '5.6.0-7.99.99'
        ],
        'conflicts' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Leonmrni\\SearchCore\\' => 'Classes',
        ],
    ],
    'state' => 'alpha',
    'clearCacheOnLoad' => 1,
    'author' => 'Daniel Siepmann',
    'author_email' => 'coding@daniel-siepmann.de',
    'version' => '1.0.0',
];
