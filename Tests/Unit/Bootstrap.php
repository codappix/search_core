<?php

$filePath = '.Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php';

if (getenv('TYPO3_VERSION') === '~7.6') {
    $filePath = '.Build/vendor/typo3/cms/typo3/sysext/core/Build/UnitTestsBootstrap.php';
}

date_default_timezone_set('UTC');

require_once dirname(dirname(__DIR__)) . '/' . $filePath;
