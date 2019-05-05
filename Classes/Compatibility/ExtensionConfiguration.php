<?php
namespace Codappix\SearchCore\Compatibility;

use Codappix\SearchCore\Bootstrap;

class ExtensionConfiguration implements ExtensionConfigurationInterface
{
    /**
     * @return object|\TYPO3\CMS\Core\Configuration\ExtensionConfiguration
     */
    private function base()
    {
        return Bootstrap::getObjectManager()->get(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class);
    }

    /**
     * @param $extensionKey
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function get($extensionKey)
    {
        return $this->base()->get($extensionKey);
    }
}
