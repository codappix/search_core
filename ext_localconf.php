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
                            Codappix\SearchCore\Command\IndexCommandController::class,
                        ],
                    ],
                    't3lib/class.t3lib_tcemain.php' => [
                        'processCmdmapClass' => [
                            $extensionKey => \Codappix\SearchCore\Hook\DataHandler::class,
                        ],
                        'processDatamapClass' => [
                            $extensionKey => \Codappix\SearchCore\Hook\DataHandler::class,
                        ],
                    ],
                ],
            ]
        );

        TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Codappix.' . $extensionKey,
            'search',
            [
                'Search' => 'search'
            ],
            [
                'Search' => 'search'
            ]
        );

        // Register different concrete implementations, depending on current TYPO3 version.
        // This way we can provide working implementations for multiple TYPO3 versions.
        $container = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class);
        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8000000) {
            $container->registerImplementation(
                \Codappix\SearchCore\Compatibility\TypoScriptServiceInterface::class,
                \Codappix\SearchCore\Compatibility\TypoScriptService::class
            );
        } else {
            $container->registerImplementation(
                \Codappix\SearchCore\Compatibility\TypoScriptServiceInterface::class,
                \Codappix\SearchCore\Compatibility\TypoScriptService76::class
            );
        }

        // API does make use of object manager, therefore use GLOBALS
        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);
        if ($extensionConfiguration === false
            || !isset($extensionConfiguration['disable.']['elasticsearch'])
            || $extensionConfiguration['disable.']['elasticsearch'] !== '1'
        ) {
            $container->registerImplementation(
                \Codappix\SearchCore\Connection\ConnectionInterface::class,
                \Codappix\SearchCore\Connection\Elasticsearch::class
            );
        }
    },
    $_EXTKEY
);
