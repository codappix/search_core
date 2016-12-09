<?php
namespace Leonmrni\SearchCore\Connection\Elasticsearch;

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

use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * The current connection to elasticsearch.
 *
 * Wrapper for Elastica\Client.
 */
class Connection implements Singleton
{
    /**
     * @var \Elastica\Client
     */
    protected $elasticaClient;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param \Elastica\Client $elasticaClient
     */
    public function __construct(\Elastica\Client $elasticaClient = null)
    {
        $this->elasticaClient = $elasticaClient;
    }

    /**
     * Inject news settings via ConfigurationManager.
     *
     * TODO: Refactor to configuration object to have a singleton holding the
     * settings with validation and propper getter?
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'SearchCore',
            'search'
        );
    }

    /**
     * Used to configure elasticaClient if no one was injected. Will use
     * injected settings for configuration.
     */
    public function initializeObject()
    {
        if ($this->elasticaClient === null) {
            $this->elasticaClient = new \Elastica\Client([
                'host' => $this->settings['host'],
                'port' => $this->settings['port'],
                // TODO: Make configurable
                // 'log' => 'file',
            ]);
            // TODO: Make configurable.
            // new \Elastica\Log($this->elasticaClient);
        }
    }

    /**
     * Get the concrete client for internal usage!
     *
     * @return \Elastica\Client
     */
    public function getClient()
    {
        return $this->elasticaClient;
    }
}
