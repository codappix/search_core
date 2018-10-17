<?php
namespace Codappix\SearchCore\Configuration;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Container of all configurations for extension.
 * Always inject this to have a single place for configuration and parsing only once.
 */
class ConfigurationContainer implements ConfigurationContainerInterface
{
    /**
     * Plain TypoScript array from extbase for extension / plugin.
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Inject settings via ConfigurationManager.
     *
     * @throws NoConfigurationException
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'SearchCore',
            'search'
        );
        if ($this->settings === null) {
            throw new NoConfigurationException('Could not fetch configuration.', 1484226842);
        }
    }

    /**
     * @param string $path In dot notation.
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $path)
    {
        $value = ArrayUtility::getValueByPath($this->settings, $path);

        if ($value === null) {
            throw new InvalidArgumentException(
                'The given configuration option "' . $path . '" does not exist.',
                InvalidArgumentException::OPTION_DOES_NOT_EXIST
            );
        }

        return $value;
    }

    /**
     * @param string $path In dot notation.
     * @return mixed
     */
    public function getIfExists(string $path)
    {
        return ArrayUtility::getValueByPath($this->settings, $path);
    }
}
