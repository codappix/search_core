<?php

(function ($extension, $table) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Codappix.' . $extension,
        'Search',
        'LLL:EXT:search_core/Resources/Private/Language/locallang_be.xlf:plugin.search.title',
        'plugin-' . $extension . '-search'
    );

    $GLOBALS['TCA'][$table]['types']['list']['subtypes_excludelist']['searchcore_search'] = 'recursive,pages';
})('search_core', 'tt_content');
