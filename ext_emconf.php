<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Search Core',
    'description' => 'Search core for implementing various search types.',
    'category' => 'be',
    'clearCacheOnLoad' => 1,
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.7-9.5.99',
            'php' => '7.2.0-7.3.99'
        ],
        'conflicts' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Codappix\\SearchCore\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'version' => '1.0.0',
    'author' => 'Daniel Siepmann',
    'author_email' => 'coding@daniel-siepmann.de',
];
