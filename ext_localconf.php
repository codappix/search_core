<?php

call_user_func(
    function ($extensionKey) {
        // TODO: Add hook for Extbase -> to handle records modified through
        // Frontend and backend modules not using datahandler

        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TYPO3_CONF_VARS'],
            [
                'SC_OPTIONS' => [
                    'extbase' => [
                        'commandControllers' => [
                            Codappix\SearchCore\Command\IndexCommandController::class,
                        ],
                    ],
                    't3lib/class.t3lib_tcemain.php' => [
                        'clearCachePostProc' => [
                            $extensionKey => \Codappix\SearchCore\Hook\DataHandler::class . '->clearCachePostProc',
                        ],
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

        \Codappix\SearchCore\Compatibility\ImplementationRegistrationService::registerImplementations();


        // API does make use of object manager, therefore use GLOBALS
        $extensionConfiguration = \Codappix\SearchCore\Bootstrap::getObjectManager()->get(\Codappix\SearchCore\Utility\ExtensionConfigurationInterface::class)->get($extensionKey);

        if ($extensionConfiguration === false
            || !isset($extensionConfiguration['disable']['elasticsearch'])
            || $extensionConfiguration['disable']['elasticsearch'] !== '1'
        ) {
            \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
                ->registerImplementation(
                    \Codappix\SearchCore\Connection\ConnectionInterface::class,
                    \Codappix\SearchCore\Connection\Elasticsearch::class
                );
        }
    },
    $_EXTKEY
);
