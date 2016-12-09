<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/',
    'Search Core'
);

TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Leonmrni.' . $_EXTKEY,
    'search',
    'Search Core'
);
