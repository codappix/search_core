<?php

$filePath = '.Build/vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTestsBootstrap.php';

if (getenv('TYPO3_VERSION') === '~7.6') {
    $filePath = '.Build/vendor/typo3/cms/typo3/sysext/core/Build/FunctionalTestsBootstrap.php';
}

require_once dirname(dirname(__DIR__)) . '/' . $filePath;
