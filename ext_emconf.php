<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Search Core',
    'description' => 'Search core for implementing various search types.',
    'category' => 'be',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.2-8.99.99',
            'php' => '7.0.0-7.99.99'
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
    'author' => 'Justus Leon Moroni',
    'author_email' => 'developer@leonmrni.com',
    'version' => '1.0.0',
];
