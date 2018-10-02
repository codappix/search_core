<?php

call_user_func(function ($extension, $table) {
    $plugin = TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Codappix.' . $extension,
            'search',
            'Search Core',
            'EXT:search_core/Resources/Public/Icons/Plugin.svg'
        ) ?? 'searchcore_search';

    $GLOBALS['TCA'][$table]['types']['list']['subtypes_excludelist'][$plugin] = 'recursive,pages';
}, 'search_core', 'tt_content');
