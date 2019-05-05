<?php

namespace Codappix\SearchCore\Compatibility\Version87;

use Codappix\SearchCore\Compatibility\ExtensionConfigurationInterface;

class ExtensionConfiguration implements ExtensionConfigurationInterface
{
    public function get($extensionKey)
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);
    }
}
