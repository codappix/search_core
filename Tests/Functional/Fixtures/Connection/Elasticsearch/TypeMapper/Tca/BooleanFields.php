<?php

$GLOBALS['TCA']['test_table'] = [
    'ctrl' => [
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
    ],
    'columns' => [
        'deleted' => [],
        'hidden' => [],
    ],
];
