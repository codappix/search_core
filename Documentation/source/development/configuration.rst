.. _development_configuration:

Using custom (non-typoscript) configuration
===========================================

When you are in need of your own non-typoscript configuration, you can create your own
Configuration Container using the TYPO3 Dependency Injection handler.

Example: Configuration through LocalConfiguration.php
-----------------------------------------------------

Configure your custom ext_localconf.php::

    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
        ->registerImplementation(
            \Codappix\SearchCore\Configuration\ConfigurationContainerInterface::class,
            \YourNamespace\Configuration\SearchCoreConfigurationContainer::class
        );

SearchCoreConfigurationContainer.php::

    <?php

    namespace YourNamespace\Configuration;

    use Codappix\SearchCore\Configuration\ConfigurationContainer;
    use Codappix\SearchCore\Configuration\NoConfigurationException;
    use TYPO3\CMS\Core\Utility\ArrayUtility;
    use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

    /**
     * Class SearchCoreConfigurationContainer
     * @package YourNamespace\Configuration
     */
    class SearchCoreConfigurationContainer extends ConfigurationContainer
    {
        /**
         * Inject settings via ConfigurationManager.
         *
         * @throws NoConfigurationException
         */
        public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
        {
            parent::injectConfigurationManager($configurationManager);

            // Now override settings with LocalConfiguration
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['search_core'])) {
                ArrayUtility::mergeRecursiveWithOverrule($this->settings, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['search_core']);
            }
            // Or manipulate it your own custom way.
        }
    }

