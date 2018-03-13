<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [

    // Supported values: '7.0', '7.1', '7.2', null.
    // If this is set to null,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute phan.
    "target_php_version" => '7.0',

    // Override to hardcode existence and types of (non-builtin) globals.
    // Class names should be prefixed with '\\'.
    // (E.g. ['_FOO' => '\\FooClass', 'page' => '\\PageClass', 'userId' => 'int'])
    'globals_type_map' => [
        '_EXTKEY' => 'string',
        'EM_CONF' => 'array',
    ],

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'Classes',
        '.Build/vendor',
    ],

    // A list of files to include in analysis
    'file_list' => [
        'ext_emconf.php',
        'ext_tables.php',
        'ext_localconf.php',
    ],

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to the `directory_list` as
    //       to `exclude_analysis_directory_list`.
    "exclude_analysis_directory_list" => [
        '.Build/vendor'
    ],

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'Classes',
        // 'Tests',
        '.Build/vendor',
    ],

    // The number of processes to fork off during the analysis phase.
    'processes' => 3,

    // Add any issue types (such as 'PhanUndeclaredMethod')
    // here to inhibit them from being reported
    'suppress_issue_types' => [
        'PhanDeprecatedFunction', // For now
        'PhanParamTooMany', // For now, due to ObjectManager->get()
    ],

    // A list of plugin files to execute.
    // See https://github.com/phan/phan/tree/master/.phan/plugins for even more.
    // (Pass these in as relative paths.
    // The 0.10.2 release will allow passing 'AlwaysReturnPlugin' if referring to a plugin that is bundled with Phan)
    'plugins' => [
        // checks if a function, closure or method unconditionally returns.
        'AlwaysReturnPlugin',  // can also be written as 'vendor/phan/phan/.phan/plugins/AlwaysReturnPlugin.php'
        // Checks for syntactically unreachable statements in
        // the global scope or function bodies.
        'UnreachableCodePlugin',
        'DollarDollarPlugin',
        'DuplicateArrayKeyPlugin',
        'PregRegexCheckerPlugin',
        'PrintfCheckerPlugin',
    ],
];
