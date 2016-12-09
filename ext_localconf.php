<?php

call_user_func(
    function ($extensionKey) {
        // TODO: Add hook for Extbase -> to handle records modified through
        // Frontend and backend modules not using datahandler

        $GLOBALS['TYPO3_CONF_VARS'] = TYPO3\CMS\Extbase\Utility\ArrayUtility::arrayMergeRecursiveOverrule(
            $GLOBALS['TYPO3_CONF_VARS'],
            [
                'SC_OPTIONS' => [
                    'extbase' => [
                        'commandControllers' => [
                            Leonmrni\SearchCore\Command\IndexCommandController::class,
                        ],
                    ],
                    // Not yet, first finish whole indexing through command controller as it's more important.
                    // 't3lib/class.t3lib_tcemain.php' => [
                    //     'processCmdmapClass' => [
                    //         $extensionKey => \Leonmrni\SearchCore\Hook\DataHandler::class,
                    //     ],
                    //     'processDatamapClass' => [
                    //         $extensionKey => \Leonmrni\SearchCore\Hook\DataHandler::class,
                    //     ],
                    // ],
                ],
            ]
        );

        TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Leonmrni.' . $extensionKey,
            'search',
            [
                'Search' => 'search'
            ],
            [
                'Search' => 'search' // TODO: Enable caching. But submitting form results in previous result?!
            ]
        );

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\Container\Container')
            ->registerImplementation(
                'Leonmrni\SearchCore\Connection\ConnectionInterface',
                'Leonmrni\SearchCore\Connection\Elasticsearch'
            );
    },
    $_EXTKEY
);
