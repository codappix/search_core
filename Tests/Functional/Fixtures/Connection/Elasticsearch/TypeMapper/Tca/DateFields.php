<?php

$GLOBALS['TCA']['test_table'] = [
    'ctrl' => [
        'crdate' => 'createDate',
    ],
    'columns' => [
        'createDate' => [],
        'date' => [
            'config' => [
                'eval' => 'trim,date',
            ],
        ],
        'time' => [
            'config' => [
                'eval' => 'something,time,somethingmore',
            ],
        ],
        'datetime' => [
            'config' => [
                'eval' => 'datetime,somethingmore',
            ],
        ],
    ],
];
