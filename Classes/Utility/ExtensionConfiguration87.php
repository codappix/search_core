<?php

namespace Codappix\SearchCore\Utility;

class ExtensionConfiguration87 implements ExtensionConfigurationInterface
{
    public function get($extensionKey)
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);
    }
}