<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(function ($extension, $table) {
    $plugin = ExtensionUtility::registerPlugin(
            'Codappix.' . $extension,
            'Results',
            'LLL:EXT:search_core/Resources/Private/Language/locallang_be.xlf:plugin.results.title',
            'EXT:search_core/Resources/Public/Icons/PluginResults.svg'
        ) ?? 'searchcore_results';

    $GLOBALS['TCA'][$table]['types']['list']['subtypes_excludelist'][$plugin] = 'recursive,pages';

    $plugin = ExtensionUtility::registerPlugin(
            'Codappix.' . $extension,
            'Form',
            'LLL:EXT:search_core/Resources/Private/Language/locallang_be.xlf:plugin.form.title',
            'EXT:search_core/Resources/Public/Icons/PluginForm.svg'
        ) ?? 'searchcore_form';

    $GLOBALS['TCA'][$table]['types']['list']['subtypes_excludelist'][$plugin] = 'recursive,pages';
}, 'search_core', 'tt_content');
