<?php

namespace Codappix\SearchCore;

use Codappix\SearchCore\Compatibility\ExtensionConfigurationInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class Bootstrap
{
    /**
     * @return object|ObjectManager
     */
    public static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return ExtensionConfigurationInterface
     */
    public static function getExtensionConfiguration()
    {
        return static::getObjectManager()->get(
            ExtensionConfigurationInterface::class
        );
    }
}